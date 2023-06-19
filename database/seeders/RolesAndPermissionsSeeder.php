<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $adminRole = Role::create(['name' => 'admin']);
         $userRole = Role::create(['name' => 'user']);


         $deletePost=Permission::create(['name' => 'delete post']);
         $deleteComment=Permission::create(['name' => 'delete comment']);
         $deleteUser=Permission::create(['name' => 'delete user']);
         $updatePost=Permission::create(['name' => 'update post']);
         $updateComment=Permission::create(['name' => 'update comment']);
         $updateUser=Permission::create(['name' => 'update user']);

         $adminRole->givePermissionTo([
            $deletePost,
            $deleteComment,
            $deleteUser,
            $updatePost,
            $updateComment,
            $updateUser,
         ]);
    }
}
