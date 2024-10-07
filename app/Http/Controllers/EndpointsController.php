<?php

namespace App\Http\Controllers;

use App\Models\{Endpoints, Account, Streams};
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class EndpointsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Endpoints/Index', [
            'filters' => Request::all('search'),
            'endpoints' => Endpoints::with('accounts')->orderBy('id')->filter(Request::only('search'))->paginate(10)->withQueryString()->through(
                fn($endpoints) => [
                    'id' => $endpoints->id,
                    'uuid' => $endpoints->uuid,
                    'name' => $endpoints->title,
                    'location' => $endpoints->location,
                    'stream_key' => $endpoints->stream_key,
                    'ip_addr' => $endpoints->ip_addr,
                    'port' => $endpoints->port,
                    'organization' => $endpoints->accounts->name,
                ],
            ),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Endpoints/Create',
            ['organizations' => Account::orderBy('name')->get()->map->only('id', 'name')]
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $endpoint = Request::validate([
            'uuid' => ['max:100', 'unique:endpoints'],
            'title' => ['required', 'max:100', 'unique:endpoints'],
            'location' => ['required', 'max:100'],
            'stream_key' => ['max:100', 'unique:endpoints'],
            'ip_addr' => ['required', 'max:100'],
            'port' => ['required', 'max:100'],
            'organization' => ['required', 'exists:accounts,id'],
        ]);

        $endpoint['uuid'] ?? ($endpoint['uuid'] = Str::uuid());
        $endpoint['stream_key'] ?? ($endpoint['stream_key'] = Str::random(16));
        $endpoint['account_id'] = $endpoint['organization'];
        unset($endpoint['organization']);

        Endpoints::create($endpoint);

        return Redirect::route('endpoints')->with('success', 'Endpoint created.');
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
                'organization' => $endpoint->account_id,
                'streams' => $endpoint->streams()->orderBy('created_at')->get()->map->only('id', 'title', 'description', 'created_at'),
            ],

            'organizations' => Account::orderBy('name')->get()->map->only('id', 'name'),
        ]);
    }

    public function update(Endpoints $endpoint): RedirectResponse
    {
        $updatedEndpoint = Request::validate([
            'uuid' => ['required', 'max:100', 'unique:endpoints,uuid,' . $endpoint->id],
            'title' => ['required', 'max:100', 'unique:endpoints,title,' . $endpoint->id],
            'location' => ['required', 'max:100'],
            'stream_key' => ['required', 'max:100', 'unique:endpoints,stream_key,' . $endpoint->id],
            'ip_addr' => ['required', 'max:100'],
            'port' => ['required', 'max:100'],
            'organization' => ['required', 'exists:accounts,id'],
        ]);

        $updatedEndpoint['account_id'] = $updatedEndpoint['organization'];
        unset($updatedEndpoint['organization']);

        $endpoint->update($updatedEndpoint);

        return Redirect::back()->with('success', 'Endpoint updated.');
    }

    public function destroy(Endpoints $endpoint): RedirectResponse
    {
        $endpoint->delete();
        //Delete streams from the endpoint
        Streams::where('endpoint_id', $endpoint->id)->delete();

        return Redirect::route('endpoints')->with('success', 'Endpoint deleted.');
    }
}
