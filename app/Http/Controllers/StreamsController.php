<?php

namespace App\Http\Controllers;

use App\Models\{Streams,Endpoints};
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class StreamsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Streams/Index', [
            'filters' => Request::all('search'),
            'streams' => Streams::with('endpoint','endpoint.accounts')->orderBy('id')->filter(Request::only('search'))->paginate(10)->withQueryString()->through(
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
        return Inertia::render('Streams/Create',
            ['endpoints' => Endpoints::orderBy('title')->get()->map->only('id', 'title')]
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $stream = Request::validate([
            'uuid' => ['required', 'max:100', 'unique:streams'],
            'title' => ['required', 'max:100', 'unique:streams'],
            'description' => ['required', 'max:100'],
            'endpoint' => ['required', 'exists:endpoints,id'],
        ]);

        $stream['slug'] ?? ($stream['slug'] = Str::slug($stream['title'].'-'.Str::random(8)));
        $stream['endpoint_id'] = $stream['endpoint'];
        unset($stream['endpoint']);

        Streams::create($stream);

        return Redirect::route('streams')->with('success', 'Stream created.');
    }

    public function edit(Endpoints $endpoint): Response
    {
        return Inertia::render('Endpoints/Edit', [
            'endpoint' => [
                'id' => $endpoint->id,
                'uuid' => $endpoint->uuid,
                'title' => $endpoint->title,
                'location' => $endpoint->location,
                'stream_key' => $endpoint->stream_key,
                'ip_addr' => $endpoint->ip_addr,
                'port' => $endpoint->port,
                'type' => $endpoint->type,
                'streams' => $endpoint->streams()->orderBy('created_at')->get()->map->only('id', 'title', 'description', 'created_at'),
            ],
        ]);
    }

    public function update(Endpoints $endpoint): RedirectResponse
    {
        $endpoint->update(
            Request::validate([
                'uuid' => ['required', 'max:100', 'unique:endpoints,uuid,' . $endpoint->id],
                'title' => ['required', 'max:100', 'unique:endpoints,title,' . $endpoint->id],
                'location' => ['required', 'max:100'],
                'stream_key' => ['required', 'max:100', 'unique:endpoints,stream_key,' . $endpoint->id],
                'ip_addr' => ['required', 'max:100'],
                'port' => ['required', 'max:100'],
                'type' => ['required', 'max:100'],
            ]),
        );

        return Redirect::back()->with('success', 'Endpoint updated.');
    }

    public function destroy(Endpoints $endpoint): RedirectResponse
    {
        $endpoint->delete();

        return Redirect::route('endpoints')->with('success', 'Endpoint deleted.');
    }
}
