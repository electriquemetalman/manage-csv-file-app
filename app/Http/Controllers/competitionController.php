<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\competitionRequest;
use App\Models\competition;
use Exception;

class competitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $competitions = Competition::orderBy('created_at', 'desc')->get();
            return response()->json($competitions, 200);
        } catch (ModelNotFoundException $exception) {
            return $exception->getMessage();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(competitionRequest $request)
    {
        //
        $validated = $request->validated();

        $csvFileName = time() . '.' . $validated['ref_file']->extension();
        $validated['ref_file']->move(public_path('ref_csvFiles/'), $csvFileName);

        $competition = Competition::create([
            'title' => $validated['title'],
            'litel_description' => $validated['litel_description'],
            'long_description' => $validated['long_description'],
            'evaluation_text' => $validated['evaluation_text'],
            'ref_file' => $csvFileName
        ]);
        if ($competition) {
            return response()->json([
                'status' => 200,
                'message' => 'Competition Added Succesfully',
                'data' => $competition,
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Somthing Went Wrong',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        try {
            $competition = Competition::findorfail($id);
            return response()->json($competition, 201);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //

        $competition = Competition::findorfail($id);

        $csvFile = $request->ref_file;

        try {
            if (isset($csvFile) === true) {

                $csvFileName = time() . '.' . $csvFile->extension();
                $csvFile->move(public_path('ref_csvFiles/'), $csvFileName);

                $competition->update([
                    'title' => $request->title,
                    'litel_description' => $request->litel_description,
                    'long_description' => $request->long_description,
                    'evaluation_text' => $request->evaluation_text,
                    'ref_file' => $csvFileName
                ]);
            } else {
                $competition->update([
                    'title' => $request->title,
                    'litel_description' => $request->litel_description,
                    'long_description' => $request->long_description,
                    'evaluation_text' => $request->evaluation_text
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => 'Competition updated successfully!',
                'updated_data' => $competition
            ], 200);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        try {
            $competition = Competition::findorfail($id);
            $competition->delete();
            return response()->json("delete succesfull", 202);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
