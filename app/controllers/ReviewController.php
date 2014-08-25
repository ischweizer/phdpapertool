<?php

use Carbon\Carbon;

class ReviewController extends BaseController{

	/**
	 * Lists all review requests.
	 */
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

	public function anyAuth()
	{
		if(Input::has('author_id') && Input::has('review_request_id') && Input::has('auth_token')){
			$author = Author::findOrFail(Input::get('author_id'));
			$reviewRequest = ReviewRequest::findOrFail(Input::get('review_request_id'));

			if(Input::has('accept'))
				DB::update('update author_review_request set answer = 1 where author_id = ? and review_request_id = ?',array(Input::get('author_id'),Input::get('review_request_id')));
			elseif(Input::has('decline'))
				DB::update('update author_review_request set answer = 0 where author_id = ? and review_request_id = ?',array(Input::get('author_id'),Input::get('review_request_id')));

			if(Input::has('file_id')){
				foreach ($reviewRequest->files as $file) {
					if ($file->id == Input::get('file_id'))
						return Response::download($file->filepath, $file->name);
				}
				App::abort(404);
			}
			$review = null;
			if(Input::has('create_review')){
				if(Input::has('message'))
					$message = Input::get('message');
				else
					$message = null;
				if(Input::hasFile('files'))
					$files = Input::file('files');
				else
					$files = null;
				$result = $this->create_review($reviewRequest, $author, $message, $files);
				$review = Review::findOrFail($result['review_id']);
			}

			$result = DB::select('select answer from author_review_request where author_id = ? and review_request_id = ? and auth_token = ?', array(Input::get('author_id'),Input::get('review_request_id'),Input::get('auth_token')));

			if(!count($result))
				App::abort(404);

			return View::make('review/email_review')
				->with('author', $author)
				->with('reviewRequest', $reviewRequest)
				->with('auth_token', Input::get('auth_token'))
				->with('answer', $result[0]->answer)
				->with('review', $review);
		} else
			App::abort(404);
	}

	/**
	 * Creates a review object and saves the uploaded files
	 *
	 * @param $reviewRequest a ReviewRequest object
	 * @param $author an Author object
	 * @param string $message the message of the review
	 * @param array $files the list of FileObject objects
	 */
	private function create_review($reviewRequest, $author, $message, $files)
	{
		$review = new Review(array('author_id' => $author->id, 'review_request_id' => $reviewRequest->id));
		if($message)
			$review->message = $message;
		$review->save();

		$errors = null;

		if ($files) {
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
					$fileObject->author_id = $author->id;
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
		return array('review_id' => $review->id, 'errors' => $errors);
	}

	/**
	 * Accepts the review request for the currently authed user
	 *
	 * @param int $id the review request id
	 */
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

	/**
	 * Declines the review request for the currently authed user
	 *
	 * @param int $id the review request id
	 */
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

	/**
	 * Shows the view for creating a review request
	 *
	 * @param int $paper_id the paper id
	 */
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

	/**
	 * Creates a ReviewRequest object from the given Input data
	 */
	public function postCreateReviewRequest(){
		if (Input::has('deadline') && Input::has('selectedAuthors') && Input::has('selectedFiles') && Input::has('paperId')) {
			$reviewRequest = new ReviewRequest(array("user_id" => Auth::user()->id, "deadline" => Input::get('deadline'), 'paper_id' => Input::get('paperId')));
			if(Input::has('message')) {
				$reviewRequest->message = Input::get('message');
				$message = Input::get('message');
			} else
				$message = null;

			$reviewRequest->save();
			$reviewRequest->authors()->sync(Input::get('selectedAuthors'));
			$reviewRequest->files()->sync(Input::get('selectedFiles'));

			//set Token for not registrered authors and send mail
			foreach ($reviewRequest->authors as $author) {
				$authorName = $author->first_name." ".$author->last_name;
				if(!$author->user){
					$auth_token = Hash::make((string)(time()+rand()));
					$author->pivot->auth_token = $auth_token;
					$author->pivot->save();

					Mail::send('emails/review_unregistered_author', array('name' => $authorName, 'reviewRequest' => $reviewRequest, 'author' => $author, 'auth_token' => $auth_token), function($message) use ($author, $authorName, $reviewRequest){
						$message->to($author->email, $authorName)
							->subject('Review Request from '.$reviewRequest->user->formatName())
							->from('noreply@da-sense.de', 'PHDPapertool');
					});
				} else {
					Mail::send('emails/review_registered_user', array('name' => $authorName, 'reviewRequest' => $reviewRequest, 'author' => $author), function($message) use ($author, $authorName, $reviewRequest){
						$message->to($author->email, $authorName)
							->subject('Review Request from '.$reviewRequest->user->formatName())
							->from('noreply@da-sense.de', 'PHDPapertool');
					});
				}

			}


			return Redirect::action('PaperController@getDetails', array(Input::get('paperId')));
		} else
			return App::abort(404);
	}

	/**
	 * Shows the details of the review with the given id
	 *
	 * @param int $id the review id
	 */
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

	/**
	 * Shows the form for creating a review
	 *
	 * @param int $reviewRequestId the review request id of the new review
	 */
	public function getCreate($reviewRequestId){

		//TODO fehlt auth check
		$reviewRequest = ReviewRequest::findOrFail($reviewRequestId);

		return View::make('review/create')
			->with('reviewRequest', $reviewRequest);
	}

	/**
	 * Creates a review from the given Input data
	 */
	public function postCreate(){

		$reviewRequest = ReviewRequest::findOrFail(Input::get('review_request_id'));
		$author = Auth::user()->author;

		$access = false;
		foreach ($reviewRequest->authors as $requested_author) {
			if($author->id == $requested_author->id){
				$access = true;
				break;
			}
		}
		if(!$access)
			App::abort(404);

		if(Input::has('message'))
			$message = Input::get('message');
		else
			$message = null;
		if(Input::hasFile('files'))
			$files = Input::file('files');
		else
			$files = null;

		$result = $this->create_review($reviewRequest, $author, $message, $files);
		return Redirect::action('ReviewController@getDetails', array('id' => $result['review_id']))->withErrors($result['errors']);
	}

}
