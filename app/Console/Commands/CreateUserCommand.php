<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new user.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = [];
        $user['name'] = $this->ask('name of the new User');
        $user['email'] = $this->ask('email of the new User');
        $user['password'] = bcrypt($this->secret('password of the new User'));
        $roleName = $this->choice('What\'s the user role?', ['admin', 'editor'], 1);

        // $roleId = Role::query()
        //     ->limit(1)
        //     ->where('name', $roleName)
        //     ->firstOrFail()
        //     ->id;

        if(!$this->validateUser($user)) return -1;
        
        DB::transaction(function() use ($user, $roleName){
            $user = User::create($user);
            //$user = $user->roles()->attach($roleId);
            $user = $user->assignRole($roleName);
        });

        $this->info('user '.$user['name'].' with email '.$user['email'].' created successfully via artisan command');
    }

    private function validateUser(array $user): bool
    {
        $validator = Validator::make($user, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
         
            return false;
        }
        return true;
    }
}

