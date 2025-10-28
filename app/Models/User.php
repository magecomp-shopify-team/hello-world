<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Osiset\ShopifyApp\Contracts\ShopModel as IShopModel;
use Osiset\ShopifyApp\Traits\ShopModel;


class User extends Authenticatable implements IShopModel
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use ShopModel; 

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // 'password' => 'hashed',
        ];
    }


    public function graph($query, $variables = [])
 
    {
 
        $body = $this->api()->graph($query, $variables);
        $body = json_decode(json_encode($body), true);
        if (isset($body['body']['extensions']['cost']['throttleStatus']['currentlyAvailable'])) {
 
            $currentlyAvailable = $body['body']['extensions']['cost']['throttleStatus']['currentlyAvailable'];
 
            if ($currentlyAvailable < 200) {
 
                sleep(2);
 
            }
 
        }
        return $body;
 
    }
}
