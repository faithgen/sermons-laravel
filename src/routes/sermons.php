<?php
/**
 * Handles sermons details
 */
Route::name('sermons.')->prefix('sermons/')->group(function () {
    Route::get('/', 'SermonController@index');
    Route::get('/{sermon}', 'SermonController@view');
    Route::post('create', 'SermonController@create')->middleware('source.site');
    Route::delete('delete', 'SermonController@delete')->middleware('source.site');
    Route::post('/update-picture', 'SermonController@updatePicture')->middleware('source.site');
    Route::post('/update', 'SermonController@update')->middleware('source.site');
});
