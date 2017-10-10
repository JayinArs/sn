<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App;
use Form;

class PaginationHelper
{
	public function __construct()
	{

	}

	/**
	 * @param $items
	 * @param int $perPage
	 *
	 * @return LengthAwarePaginator
	 */
	public function paginate( $items, $perPage = 12 )
	{
		$currentPage      = LengthAwarePaginator::resolveCurrentPage();
		$currentPageItems = array_slice( $items, ( $currentPage - 1 ) * $perPage, $perPage );

		return new LengthAwarePaginator( $currentPageItems, count( $items ), $perPage );
	}
}