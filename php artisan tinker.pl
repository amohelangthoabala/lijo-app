php artisan tinker

use Spatie\Permission\Models\Role;

Role::create(['name' => 'admin']);
Role::create(['name' => 'restaurant']);
Role::create(['name' => 'order-handler']);