<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\MasterConfig;
use App\Models\PencatatanAir;
use App\Models\Tagihan;
use App\Models\TransaksiKas;
use App\Models\Anggota;
use App\Models\TabunganUmroh;
use App\Policies\UserPolicy;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\PencatatanAirPolicy;
use App\Policies\TagihanPolicy;
use App\Policies\TransaksiKasPolicy;
use App\Policies\MasterConfigPolicy;
use App\Policies\AnggotaPolicy;
use App\Policies\TabunganUmrohPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        PencatatanAir::class => PencatatanAirPolicy::class,
        Tagihan::class => TagihanPolicy::class,
        TransaksiKas::class => TransaksiKasPolicy::class,
        MasterConfig::class => MasterConfigPolicy::class,
        Anggota::class => AnggotaPolicy::class,
        TabunganUmroh::class => TabunganUmrohPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
