<?php

class ReviewController extends BaseController{

	public function getIndex()
	{
		/*$reviews = DB::table('reviews')
			->join('review_user', 'reviews.id', '=', 'review_user.review_id')
			->whereNull('answer')
			->get();*/

		/*$reviews = array();
		foreach(Review::where('user_id', '=', Auth::user()->id)->get() as $review){
			if($review->answer == null)
				array_push($reviews, $review);
		}*/

		$reviewRequests = array();
		foreach(Auth::user()->reviewRequests as $reviewRequest){
			if(is_null($reviewRequest->answer))
				array_push($reviewRequests, $reviewRequest);
		}

		return View::make('review/handle_requests')->with('reviewRequests', $reviewRequests);
	}


}