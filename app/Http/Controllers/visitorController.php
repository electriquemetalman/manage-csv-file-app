<?php

namespace App\Http\Controllers;

use App\Models\visitor;
use App\Models\competition;
use Illuminate\Http\Request;
use App\Http\Requests\visitorRequest;
use Illuminate\Support\Facades\Storage;
use Exception;

class visitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(visitor $visitorModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, visitor $visitorModel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(visitor $visitorModel)
    {
        //
    }

    /**
     * get ranking for specific competition.
     */

    public function ranking($id)
    {
        try {
            $visitors = visitor::where('competition_id', $id)->orderBy('result', 'desc')->get();
            //$visitor_nomber = visitor::count();
            return response()->json($visitors, 201);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * compare file and return result into database.
     */
    public function compareFile(visitorRequest $request, $id)
    {
        //cette fonction permet de lire les fichier csv et retourner sous forme de tableau
        function read($csv)
        {
            $file = fopen($csv, 'r');

            $header = fgetcsv($file);
            $conten_file = [];
            while ($row = fgetcsv($file)) {
                $conten_file[] = array_combine($header, $row);
            }

            fclose($file);
            return $conten_file;
        }

        // cette fonction retourne l'entete d'un fichier csv
        function read_header($csv)
        {
            $file = fopen($csv, 'r');

            $header = fgetcsv($file);

            fclose($file);
            return $header;
        }
        // validation
        $validated = $request->validated();

        //recuperation de la competition selectioner
        $competition = Competition::findorfail($id);

        //recuperation de l'entete des 2 fichiers
        $header_csv = read_header($validated['file']);
        $header_ref_csv = read_header(public_path('ref_csvFiles/' . $competition->ref_file));

        //comparer les entetes des 2 fichier
        if ($header_csv === $header_ref_csv) {

            //lecture des 2 fichiers csv
            $csv = read($validated['file']);
            $ref_csv = read(public_path('ref_csvFiles/' . $competition->ref_file));

            //declaration des 2 tables pour stocker les valeur de la colone images de chaque fichier
            $ref_array = [];
            $visitor_array = [];

            //parcour des tableau et ajout des valeur de la colone images dans les tableau respectif
            foreach ($csv as $value) {
                array_push($visitor_array, $value['Images']);
            }

            foreach ($ref_csv as $value) {
                array_push($ref_array, $value['Images']);
            }

            //verification de l'egaliter des valeur de la colone images des 2 tableau
            $result = array_diff($ref_array, $visitor_array);

            if (empty($result)) {

                //declaration des 2 tables pour stocker les valeur de la colone predicted class de chaque fichier
                $ref_predict_array = [];
                $visitor_predict_array = [];

                //parcour des tableau et ajout des valeur de la colone predicted class dans les tableau respectif
                foreach ($csv as $value) {
                    array_push($visitor_predict_array, $value['Predicted class']);
                }

                foreach ($ref_csv as $value) {
                    array_push($ref_predict_array, $value['Predicted class']);
                }

                //recuperation du nombre de ligne du fichier ref
                $N = sizeof($ref_predict_array);

                //verification du nombre de celule semblable de la colone predicted class des 2 tableau
                $result2 = array_diff_assoc($ref_predict_array, $visitor_predict_array);
                //calcule du nombre de celule correcte
                $n = $N - sizeof($result2);

                //calcule du resultat finale
                $final_result = 100 * $n / $N;

                $competition = Visitor::create([
                    'competition_id' => $id,
                    'name' => $validated['name'],
                    'matricule' => $validated['matricule'],
                    'result' => $final_result
                ]);

                return response()->json([
                    'status' => 201,
                    'message' => 'Your File Added Succesfully',
                    'data' => $competition,
                ]);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Somthing Went Wrong: your File is incorrect',
                ]);
            }
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Somthing Went Wrong: your File is incorrect',
            ]);
        }

        //dd($csv);
        //dd(sizeof($csv), count($ref_csv), count(current($csv)), count(current($ref_csv)));
        //dd(public_path('ref_csvFiles/' . $competition->ref_file));
    }
}
