<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','kana','tel','email',
        'postcode','address', 'birthday','gender', 'memo'];

    //scope必ず先頭に付ける,$query必ず一番目
    public function scopeSearchCustomers($query, $input = null)
    {
        if(!empty($input)){
            if(Customer::where('kana', 'like', $input . '%' )
            ->orWhere('tel', 'like', $input . '%')->exists())
            {
                return $query->where('kana', 'like', $input . '%' )
                ->orWhere('tel', 'like', $input . '%');
            }
        }
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

}
