<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::lastest()->get();
        $res = [
            'success' => true,
            'data' => $events,
            'message' => 'List posts',
        ];
        return response()->json($res, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul_event' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
            'tanggal_event' => 'required|date',
            'lokasi' => 'required',
            'foto' => 'required|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $event = new Event;
        $event->judul_event = $request->judul_event;
        $event->deskripsi = $request->deskripsi;
        $event->tanggal_event = $request->tanggal_event;
        $event->lokasi = $request->lokasi;
        if ($request->hasFile('foto')){
            $path = $request->file('foto')->store('events', 'public');
            $event->foto = $path;
        }
        $event->save();

        $res = [
            'success' => true,
            'data' => $event,
            'message' => 'Lists event'
        ];
        return response()->json($res, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        $Event = Event::find($id);
        if (! $Event){
            return response()->json([
                'message' => 'Data Not Found',
            ], 401);
        }
        return response()->json([
            'success' => true,
            'data' => $Event,
            'message' => 'Show Event Detail'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'judul_event' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
            'tanggal_event' => 'required|date',
            'lokasi' => 'required',
            'foto' => 'required|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $event = new Event;
        $event->judul_event = $request->judul_event;
        $event->deskripsi = $request->deskripsi;
        $event->tanggal_event = $request->tanggal_event;
        $event->lokasi = $request->lokasi;
        if ($request->hasFile('foto')){
            if ($event->foto && Storage::disk('public')->exists($event->foto)){
                Storage::disk('public')->delete($event->foto);
            
            $path = $request->file('foto')->store('events', 'public');
            $event->foto = $path;
            }
        }
        $event->save();

        $res = [
            'success' => true,
            'data' => $event,
            'message' => 'Store event'
        ];
        return response()->json($res, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::find($id);
        if (! $event) {
            return response()->json(['message'=>'Data Not Found'], 404);
        }
        if ($event->foto && Storage::disk('public')->exists($event->foto)){
            Storage::disk('public')->delete($event->foto);
        }

        $event->delete();
        return response()->json([
            'data' => [],
            'message' => 'Post deleted successfully',
            'success' => true
        ]);
    }
}
