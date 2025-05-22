<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Polyline;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PolylineController extends Controller
{
    protected $polyline;
    public function __construct()
    {
        $this->polyline = new Polyline();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $polylines = $this->polyline->polylines();

        foreach ($polylines as $p) {
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
            $path = $image->store('polylines', 's3'); // simpan di folder 'images' di bucket S3

            $imageUrl = Storage::disk('s3')->url($path);
        }

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'geom' => $request->geom,
            'image' => $imageUrl
        ];

        if (!$this->polyline->create($data)) {
            return redirect()->back()->with('error', 'Failed to create polyline');
        }

        return redirect()->back()->with('success', 'Polyline created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $polyline = $this->polyline->polyline($id);

        foreach ($polyline as $p) {
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
        $polyline = $this->polyline->find($id);
        // dd($polyline);
        $data = [
            'id' => $id,
            'title' => 'Edit Polyline',
            'polyline' => $polyline,
        ];
        return view('edit-polyline', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $polyline = $this->polyline->find($id);
        if (!$polyline) {
            return redirect()->back()->with('error', 'Polyline not found');
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

        if (!$polyline->update($data)) {
            return redirect()->back()->with('error', 'Failed to update polyline');
        }

        return redirect()->back()->with('success', 'Polyline updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $polyline = $this->polyline->find($id);
        if (!$polyline) {
            return redirect()->back()->with('error', 'Polyline not found');
        }

        if ($polyline->image) {
            $parsedUrl = parse_url($polyline->image);
            $path = ltrim($parsedUrl['path'], '/');

            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
        }

        if (!$polyline->delete()) {
            return redirect()->back()->with('error', 'Failed to delete polyline');
        }

        return redirect()->back()->with('success', 'Polyline deleted successfully');
    }
}
