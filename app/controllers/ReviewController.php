<?php

use Carbon\Carbon;

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

		$unansweredReviewRequests = array();
		$acceptedReviewRequests = array();
		$finishedReviewRequests = array();
		foreach(Auth::user()->author->reviewRequests as $reviewRequest){
			if(is_null($reviewRequest->pivot->answer))
				array_push($unansweredReviewRequests, $reviewRequest);
			else if($reviewRequest->pivot->answer){
				$answered = false;
				foreach ($reviewRequest->reviews as $review) {
					if($review->author_id == Auth::user()->author_id){
						$answered = true;
						array_push($finishedReviewRequests, $reviewRequest);
						break;
					}
				}
				if(!$answered)
					array_push($acceptedReviewRequests, $reviewRequest);
					
			}
		}

		return View::make('review/handle_requests')
			->with('unansweredReviewRequests', $unansweredReviewRequests)
			->with('acceptedReviewRequests', $acceptedReviewRequests)
			->with('finishedReviewRequests', $finishedReviewRequests);

	}

	public function getAuth()
	{
		if(Input::has('author_id') && Input::has('review_request_id') && Input::has('email') && Input::has('auth_token')){
			$author = Author::findOrFail(Input::get('author_id'));
			if($author->email == Input::get('email')){
				foreach ($author->reviewRequests as $reviewRequest) {
					if($reviewRequest->id == Input::get('review_request_id')
						&& $reviewRequest->pivot->auth_token == Input::get('auth_token')){
						return $this->getCreate(Input::get('review_request_id'));
					}
				}
			}
		} 

		App::abort(404);
	}

	public function getAccept($id)
	{
		$reviewRequest = ReviewRequest::findOrFail($id);

		$access = false;
		foreach($reviewRequest->authors as $author){
			if($author->id == Auth::user()->author_id){
				$access = true;
				$author->pivot->answer = true;
				$author->pivot->save();
				break;
			}
		} 
		if(!$access)
			App::abort(404);

		return Redirect::to('review');
	}

	public function getDecline($id)
	{
		$reviewRequest = ReviewRequest::findOrFail($id);

		$access = false;
		foreach($reviewRequest->authors as $author){
			if($author->id == Auth::user()->author_id){
				$access = true;
				$author->pivot->answer = false;
				$author->pivot->save();
				break;
			}
		} 
		if(!$access)
			App::abort(404);

		return Redirect::to('review');
	}

	public function getCreateRequest($paper_id){

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
		foreach (Author::notAdmin()->get() as $author) {
				$authorNames[$author->id] = $author->formatName();
		}

		$fileNames = array();
		foreach ($paper->files()->orderby('created_at', 'desc')->get() as $file) {
			$fileNames[$file->id] = $file->formatName();
		}

		return View::make('review/createRequest')
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
			$reviewRequest->authors()->sync(Input::get('selectedAuthors'));
			$reviewRequest->files()->sync(Input::get('selectedFiles'));
			
			//set Token for not registrered authors
			foreach ($reviewRequest->authors() as $author) {
				if(!$author->user){
					$author->pivot->auth_token = Hash::make(time()+rand()); //Not tested
					$author->save();
				}
			}

			//TODO Send email

			return Redirect::action('PaperController@getDetails', array(Input::get('paperId')));
		} else
			return App::abort(404);
	}

	public function getDetails($id){
		$review = Review::find($id);
		if(is_null($review))
			App::abort(404);

		$access = false;
		if($review->author_id == Auth::user()->author->id)
			$access = true;

		if(!$access){
			foreach($review->reviewRequest->paper->authors as $author){
				if($author->id == Auth::user()->author->id)
					$access = true;
			}
			if(!$access)
				App::abort(404);
		}

		return View::make('review/detail')->with('review', $review);
	}

	public function getCreate($reviewRequestId){

		$reviewRequest = ReviewRequest::findOrFail($reviewRequestId);

		return View::make('review/create')
			->with('reviewRequest', $reviewRequest);
	}

	public function postCreate(){
		if(Input::has('reviewRequestId') /*&& Input::has('files')*/){
			$reviewRequest = ReviewRequest::findOrFail(Input::get('reviewRequestId'));

			$review = new Review(array('author_id' => Auth::user()->author->id, 'review_request_id' => Input::get('reviewRequestId')));
			if(Input::has('message'))
				$review->message = Input::get('message');
			$review->save();
			
			$errors = null;
			if (Input::hasFile('files')) {
				$files = Input::file('files');
	
				foreach ($files as $file) {
					$destinationPath = storage_path().'/uploads/';
					
					if(!File::isDirectory($destinationPath))
					{
					     File::makeDirectory($destinationPath);
					}
					
					$filename = time()."_".$file->getClientOriginalName();
					$uploadSuccess = $file->move($destinationPath, $filename);
					
					if($uploadSuccess) {
						$fileObject = new FileObject();
						$fileObject->author_id = Auth::user()->author->id;
						$fileObject->paper_id = $review->reviewRequest->paper->id;
						$fileObject->review_id = $review->id;
						$fileObject->name = $file->getClientOriginalName();
						$fileObject->filepath = $destinationPath.$filename;
						$fileObject->comment = '';
						$fileObject->save();
					} else {
						$errors = array('message' => 'Couldn\'t save some files.');
					}
				}
			}
			return Redirect::action('ReviewController@getDetails', array('id' => $review->id))->withErrors($errors);
		} else 
			App::abort(404);
	}
	
	public function getFiles() {
		$review = Review::find(1);
		$files = $review->files()->get();
		foreach($files as $file) {
			echo $file->formatName();
		}
	}

}