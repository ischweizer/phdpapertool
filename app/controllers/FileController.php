<?php

use Carbon\Carbon;

class FileController extends BaseController {

	public function getFile($id) {
		$file = FileObject::with('paper', 'paper.authors')->find($id);

		if (is_null($file)) {
			App::abort(404);
		}

		if (!$this->checkFileAccess($file)) {
			App::abort(404);
		}

		return Response::download($file->filepath, $file->name);
	}

	/**
	 * Handle file uploads for given paper
	 */
	public function postUploadFile($paperId) {
		if (!is_null($paperId)) {
			$paper = Paper::with('authors')->find($paperId);
			if (!is_null($paper)) {
				$files = Input::file('files');

				$fileNames = array();
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
						$fileObject->paper_id = $paper->id;
						$fileObject->name = $file->getClientOriginalName();
						$fileObject->filepath = $destinationPath.$filename;
						$fileObject->comment = '';
						$fileObject->save();

						$fileNames[$fileObject->id] = $fileObject->formatName();
					} else {
						return Response::json(array('success' => 0, 'error' => 'Error uploading file'));
					}
				}

				return Response::json(array('success' => 1, 'files' => $fileNames));
			} else {
				return Response::json(array('success' => 0, 'error' => 'No Paper with given id found!'));
			}
		} else {
			return Response::json(array('success' => 0, 'error' => 'No Paper id given!'));
		}
	}

	/**
	 * Return Edit-File view
	 */
	public function getEditFile($id) {
		if (!is_null($id)) {
			$file = FileObject::with('paper')->find($id);

			return View::make('file/edit', array('model' => $file, 'edit' => true));
		}
	}

	public function getFileDetails($id) {
		if (!is_null($id)) {
			$file = FileObject::with('paper')->find($id);

			return View::make('file/edit', array('model' => $file, 'edit' => false));
		}
	}

	/**
	 * Update File
	 */
	public function postEditFile($id) {
		if (!is_null($id)) {
			$validator = FileObject::validate(Input::all());

			if ($validator->fails()) {
				return Redirect::action('FileController@getEditFile')->withErrors($validator)->withInput();
			}
			$file = FileObject::find($id);
			$file->fill(Input::all());

			$success = $file->save();
			// check for success
			if (!$success) {
				return Redirect::action('FileController@getEditFile')->
					withErrors(new MessageBag(array('Sorry, couldn\'t save file to database.')))->
					withInput();
			}

			return Redirect::action('FileController@getFileDetails', $id);
		}
		App::abort(404);
	}

	private function checkFileAccess($file) {
		$paperAccess = $this->checkPaperAccess($file->paper);

		if ($paperAccess) {
			return true;
		}

		foreach ($file->reviewRequests as $reviewRequest) {
			if($this->checkReviewRequestAccess($reviewRequest))
				return true;
		}

		return false;
	}

	/**
	 * Checks whether the currently authed user is an author of the given paper
	 *
	 * @param $paper the paper model
	 */
	private function checkPaperAccess($paper) {
		foreach ($paper->authors as $author) {
			if ($author->id == Auth::user()->author->id) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Checks whether the currently authed user is an requested Author of this paper
	 *
	 * @param $reviewRequest the ReviewRequest model
	 */
	private function checkReviewRequestAccess($reviewRequest){
		foreach ($reviewRequest->authors as $author) {
			if($author->id == Auth::user()->author_id)
				return true;
		}
		return false;
	}
}
