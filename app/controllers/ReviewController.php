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
		foreach(Auth::user()->author->reviewRequests as $reviewRequest){
			if(is_null($reviewRequest->pivot->answer))
				array_push($reviewRequests, $reviewRequest);
		}

		return View::make('review/handle_requests')->with('reviewRequests', $reviewRequests);
	}

	public function getCreate($paper_id){

		$paper = Paper::find($paper_id);
		if(!$paper)
			App::abort(404);

		$access = false;
		foreach ($paper->authors as $author) {
			if(Auth::user()->author->id == $author->id)
				$access = true;
		}
		if(!$access)
			App::abort(404);

		$authorNames = array();
		foreach (Author::notAdmin()->where('id', '<>', Auth::user()->author->id)->get() as $author) {
				$authorNames[$author->id] = $author->formatName();
		}

		$fileNames = array();
		foreach ($paper->files as $file) {
			$fileNames[$file->id] = $file->name;
		}

		return View::make('review/create')
			->with('paper', $paper)
			->with('fileNames', $fileNames)
			->with('authorNames', $authorNames);
	}

	public function postCreateReviewRequest(){
		if (Input::has('deadline') && Input::has('selectedAuthors') && Input::has('selectedFiles') && Input::has('paperId')) {
			$reviewRequest = new ReviewRequest(array("user_id" => Auth::user()->id, "deadline" => Input::get('deadline'), 'paper_id' => Input::get('paperId')));
			if(Input::has('message'))
				$reviewRequest->message = Input::get('message');
			$reviewRequest->save();
			$reviewRequest->requestedAuthors()->sync(Input::get('selectedAuthors'));
			$reviewRequest->files()->sync(Input::get('selectedFiles'));
			
			return Redirect::action('PaperController@getDetails', array(Input::get('paperId')));
		} else
			return App::abort(404);
	}

	public function getDetails(){
		//TODO
	}


}