<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Auth\Actions\CreerUtilisateur;
use Modules\Auth\Actions\ModifierUtilisateur;
use Modules\Auth\Actions\SupprimerUtilisateur;
use Modules\Auth\Http\Requests\CreerUtilisateurRequest;
use Modules\Auth\Http\Requests\ModifierUtilisateurRequest;
use Spatie\Permission\Models\Role;

/**
 * Gestion des utilisateurs par les administrateurs.
 *
 * Chaque méthode est un adaptateur HTTP mince : elle reçoit la requête,
 * délègue à l'Action correspondante et retourne une réponse.
 * Aucune logique métier ici.
 */
final class UserController extends Controller
{
    public function index(): View
    {
        return view('auth::users.index');
    }

    public function store(CreerUtilisateurRequest $request, CreerUtilisateur $action): RedirectResponse
    {
        $action(
            $request->nom(),
            $request->email(),
            $request->password(),
            $request->roleId(),
        );

        return redirect()
            ->route('auth.users.create')
            ->with('notify_success', __('auth::labels.notif_user_cree'));
    }

    public function edit(User $user): View
    {
        return view('auth::users.edit', [
            'user'  => $user,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(ModifierUtilisateurRequest $request, User $user, ModifierUtilisateur $action): RedirectResponse
    {
        $action($user->id, $request->nom(), $request->email(), $request->roleId());

        return redirect()
            ->route('auth.users.index')
            ->with('notify_success', __('auth::labels.notif_user_modifie'));
    }

    public function destroy(User $user, SupprimerUtilisateur $action): RedirectResponse
    {
        $action($user->id);

        return redirect()
            ->route('auth.users.index')
            ->with('notify_success', __('auth::labels.notif_user_supprime'));
    }
}
