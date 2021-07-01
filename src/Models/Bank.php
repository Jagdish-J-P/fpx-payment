<?php

namespace JagdishJP\FpxPayment\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model {

	public const STATUS_ONLINE = 'Online';
	public const STATUS_OFFLINE = 'Offline';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'bank_id',
		'name',
		'short_name',
		'status',
	];

	public function isOnline() {
		return $this->status === self::STATUS_ONLINE;
	}

	public function isOffline() {
		return $this->status === self::STATUS_OFFLINE;
	}

	public function getNameAttribute()
	{

		return $this->short_name . ($this->isOffline() ? " (offline)" : '');
	}
}
