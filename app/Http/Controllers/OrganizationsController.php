<?php

namespace App\Http\Controllers;

use App\Models\{Organization, Account};
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Organizations/Index', [
            'filters' => Request::all('search', 'online'),
            'organizations' => Account::orderBy('name')
                ->filter(Request::only('search'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($organization) => [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'users' => $organization->users()->count(),
                ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Organizations/Create');
    }

    public function store(): RedirectResponse
    {
        Account::create(
            Request::validate([
                'name' => ['required', 'max:100'],
            ])
        );

        return Redirect::route('organizations')->with('success', 'Organization created.');
    }

    public function edit(Account $organization): Response
    {
        return Inertia::render('Organizations/Edit', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'users' => $organization->users()->orderByName()->get()->map->only('id', 'name', 'email', 'phone'),
            ],
        ]);
    }

    public function update(Account $organization): RedirectResponse
    {
        $organization->update(
            Request::validate([
                'name' => ['required', 'max:100'],
            ])
        );

        return Redirect::back()->with('success', 'Organization updated.');
    }

    public function destroy(Account $organization): RedirectResponse
    {
        $organization->delete();

        return Redirect::route('organizations')->with('success', 'Organization deleted.');
    }

    public function restore(Account $organization): RedirectResponse
    {
        $organization->restore();

        return Redirect::back()->with('success', 'Organization restored.');
    }
}
