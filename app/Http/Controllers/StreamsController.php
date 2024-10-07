<?php

namespace App\Http\Controllers;

use App\Models\{Streams, Endpoints};
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class StreamsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Streams/Index', [
            'filters' => Request::all('search'),
            'streams' => Streams::with('endpoint', 'endpoint.accounts')->orderBy('id')->filter(Request::only('search'))->paginate(10)->withQueryString()->through(
                fn($streams) => [
                    'id' => $streams->id,
                    'uuid' => $streams->uuid,
                    'name' => $streams->title,
                    'slug' => $streams->slug,
                    'description' => $streams->description,
                    'endpoint' => $streams->endpoint->title,
                    'organization' => $streams->endpoint->accounts->name,
                ],
            ),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Streams/Create', ['endpoints' => Endpoints::orderBy('title')->get()->map->only('id', 'title')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $stream = Request::validate([
            'uuid' => ['required', 'max:100', 'unique:streams'],
            'title' => ['required', 'max:100', 'unique:streams'],
            'description' => ['required', 'max:100'],
            'endpoint' => ['required', 'exists:endpoints,id'],
        ]);

        $stream['slug'] ?? ($stream['slug'] = Str::slug($stream['title'] . '-' . Str::random(8)));
        $stream['endpoint_id'] = $stream['endpoint'];
        unset($stream['endpoint']);

        Streams::create($stream);

        return Redirect::route('streams')->with('success', 'Stream created.');
    }

    public function edit(Streams $stream): Response
    {
        return Inertia::render('Streams/Edit', [
            'stream' => [
                'id' => $stream->id,
                'uuid' => $stream->uuid,
                'title' => $stream->title,
                'slug' => $stream->slug,
                'description' => $stream->description,
                'endpoint' => $stream->endpoint,
            ],
            'endpoints' => Endpoints::orderBy('title')->get()->map->only('id', 'title'),
        ]);
    }

    public function update(Streams $stream): RedirectResponse
    {
        $updatedStream = Request::validate([
            'uuid' => ['required', 'max:100', 'unique:streams,uuid,' . $stream->id],
            'title' => ['required', 'max:100', 'unique:streams,title,' . $stream->id],
            'slug' => ['required', 'max:100', 'unique:streams,slug,' . $stream->id],
            'description' => ['required', 'max:100'],
            'endpoint' => ['required', 'exists:endpoints,id'],
        ]);

        $updatedStream['slug'] ?? ($updatedStream['slug'] = Str::slug($updatedStream['title'] . '-' . Str::random(8)));
        $updatedStream['endpoint_id'] = $updatedStream['endpoint'];
        unset($updatedStream['endpoint']);

        $stream->update($updatedStream);

        return Redirect::back()->with('success', 'Stream updated.');
    }

    public function destroy(Endpoints $endpoint): RedirectResponse
    {
        $endpoint->delete();

        return Redirect::route('endpoints')->with('success', 'Endpoint deleted.');
    }

    public function getRemoteSdp(): string
    {
        $validatedRequest = Request::validate([
            'stream_uuid' => ['required', 'max:100', 'exists:streams,uuid'],
            'data' => ['required'],
        ]);
        $stream = Streams::where('uuid', $validatedRequest['stream_uuid'])->with('endpoint')->first();

        $httpCall = Http::withHeaders([
            'Authorization' => $stream->endpoint->stream_key,
        ])->asForm()->post($stream->endpoint->ip_addr . ':' . $stream->endpoint->port . '/stream/receiver/'.$stream->uuid,[
            'suuid' => $stream->uuid,
            'data' => $validatedRequest['data'],
        ]);

        if($httpCall->failed()){
            return response()->json(['error' => 'Failed to get remote sdp'], 500);
        }

        return $httpCall->body();
    }

    public function saveIceCandidates(): string
    {
        $validatedRequest = Request::validate([
            'stream_uuid' => ['required', 'max:100', 'exists:streams,uuid'],
            'session_id' => ['required'],
            'candidates' => ['required'],
        ]);
        $stream = Streams::where('uuid', $validatedRequest['stream_uuid'])->with('endpoint')->first();

        $httpCall = Http::withHeaders([
            'Authorization' => $stream->endpoint->stream_key,
        ])->asForm()->post($stream->endpoint->ip_addr . ':' . $stream->endpoint->port . '/stream/ice/send',[
            'session_id' => $validatedRequest['session_id'],
            'ice_candidate' => $validatedRequest['candidates'],
        ]);

        if($httpCall->failed()){
            return response()->json(['error' => 'Failed to save ice candidates'], 500);
        }

        //Get ICE candidates from the stream
        $httpCall = Http::withHeaders([
            'Authorization' => $stream->endpoint->stream_key,
        ])->asForm()->get($stream->endpoint->ip_addr . ':' . $stream->endpoint->port . '/stream/ice/get',[
            'session_id' => $validatedRequest['session_id'],
        ]);

        if($httpCall->failed()){
            return response()->json(['error' => 'Failed to get ice candidates'], 500);
        }

        return $httpCall->body();
    }
}
