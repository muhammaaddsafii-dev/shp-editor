<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Point;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PointController extends Controller
{
    protected $point;
    public function __construct()
    {
        $this->point = new Point();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $points = $this->point->points();

        foreach ($points as $p) {
            $feature[] = [
                'type' => 'Feature',
                'geometry' => json_decode($p->geom),
                'properties' => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'description' => $p->description,
                    'image' => $p->image,
                    'created_at' => $p->created_at,
                    'updated_at' => $p->updated_at
                ]
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $feature,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'geom' => 'required',
                'image' => 'mimes:jpeg,jpg,png,gif,tiff|max:10000' // 10MB
            ],
            [
                'name.required' => 'Name is required',
                'geom.required' => 'Location is required',
                'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, tiff, gif',
                'image.max' => 'Image must not exceed 10MB'
            ]
        );

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('points', 's3'); // simpan di folder 'images' di bucket S3

            $imageUrl = Storage::disk('s3')->url($path);
        }

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'geom' => $request->geom,
            'image' => $imageUrl
        ];

        if (!$this->point->create($data)) {
            return redirect()->back()->with('error', 'Failed to create point');
        }

        return redirect()->back()->with('success', 'Point created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $point = $this->point->point($id);

        foreach ($point as $p) {
            $feature[] = [
                'type' => 'Feature',
                'geometry' => json_decode($p->geom),
                'properties' => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'description' => $p->description,
                    'image' => $p->image,
                    'created_at' => $p->created_at,
                    'updated_at' => $p->updated_at
                ]
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $feature,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $point = $this->point->find($id);
        // dd($point);
        $data = [
            'id' => $id,
            'title' => 'Edit Point',
            'point' => $point,
        ];
        return view('edit-point', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $point = $this->point->find($id);
        if (!$point) {
            return redirect()->back()->with('error', 'Point not found');
        }

        $request->validate(
            [
                'name' => 'required',
                'geom' => 'required',
                'image' => 'nullable|mimes:jpeg,jpg,png,gif,tiff|max:10000'
            ],
            [
                'name.required' => 'Name is required',
                'geom.required' => 'Location is required',
                'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, tiff, gif',
                'image.max' => 'Image must not exceed 10MB'
            ]
        );

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'geom' => $request->geom
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('images', 's3');
            $imageUrl = Storage::disk('s3')->url($path);
            $data['image'] = $imageUrl;
        }

        if (!$point->update($data)) {
            return redirect()->back()->with('error', 'Failed to update point');
        }

        return redirect()->back()->with('success', 'Point updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $point = $this->point->find($id);
        if (!$point) {
            return redirect()->back()->with('error', 'Point not found');
        }

        if ($point->image) {
            $parsedUrl = parse_url($point->image);
            $path = ltrim($parsedUrl['path'], '/'); 

            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
        }

        if (!$point->delete()) {
            return redirect()->back()->with('error', 'Failed to delete point');
        }

        return redirect()->back()->with('success', 'Point deleted successfully');
    }

}
