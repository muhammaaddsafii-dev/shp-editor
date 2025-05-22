<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Polygon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PolygonController extends Controller
{
    protected $polygon;
    public function __construct()
    {
        $this->polygon = new Polygon();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $polygons = $this->polygon->polygons();

        foreach ($polygons as $p) {
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
            $path = $image->store('polygons', 's3'); // simpan di folder 'images' di bucket S3

            $imageUrl = Storage::disk('s3')->url($path);
        }

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'geom' => $request->geom,
            'image' => $imageUrl
        ];

        if (!$this->polygon->create($data)) {
            return redirect()->back()->with('error', 'Failed to create polygon');
        }

        return redirect()->back()->with('success', 'Polygon created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $polygon = $this->polygon->polygon($id);

        foreach ($polygon as $p) {
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
        $polygon = $this->polygon->find($id);
        // dd($polygon);
        $data = [
            'id' => $id,
            'title' => 'Edit polygon',
            'polygon' => $polygon,
        ];
        return view('edit-polygon', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $polygon = $this->polygon->find($id);
        if (!$polygon) {
            return redirect()->back()->with('error', 'polygon not found');
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

        if (!$polygon->update($data)) {
            return redirect()->back()->with('error', 'Failed to update polygon');
        }

        return redirect()->back()->with('success', 'Polygon updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $polygon = $this->polygon->find($id);
        if (!$polygon) {
            return redirect()->back()->with('error', 'polygon not found');
        }

        if ($polygon->image) {
            $parsedUrl = parse_url($polygon->image);
            $path = ltrim($parsedUrl['path'], '/');

            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
        }

        if (!$polygon->delete()) {
            return redirect()->back()->with('error', 'Failed to delete polygon');
        }

        return redirect()->back()->with('success', 'Polygon deleted successfully');
    }
}
