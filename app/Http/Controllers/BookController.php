<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\OrderBook;
class BookController extends Controller
{
    public function getBooks($title){
        $book =  Book::where("title",$title)->get();
        return response()->json($book);
     }

     public function addBook(Request $request){
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'book_file_path' => 'required|file|mimes:pdf|max:10240', // Max file size 10MB
        ]);
        $uploadedFile = $request->file('book_file_path');
        $filename =  $uploadedFile->getClientOriginalName();
        $uploadedFilePath = $uploadedFile->storeAs('public/uploads', $filename);
        $book = new Book();
        $book->title = $request->title;
        $book->description = $request->description;
        $book->book_file_path = $uploadedFilePath;
        $results = $book->save();
            if($results){
                return response()->json(['result' => 'Book added successfully']);
            }
            else{
                return response()->json(['result' => 'Failed to add book']);

            }


     }
     public function deleteBook($id){
        $book = Book::find($id);

        if(!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        // Delete the associated file from the storage
        if (Storage::exists($book->book_file_path)) {
            Storage::delete($book->book_file_path);
        }


        $book->delete();

        return response()->json(['message' => 'Book deleted successfully'], 200);
    }

     public function editBook(Request $request, $id) {

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);


        $book = Book::find($id);
        if ($book) {

        $book->title = $request->title;
        $book->description = $request->description;
        $book->update();
        return response()->json(['result' => 'Book updated successfully']);

        }
         else {
            return response()->json(['result' => 'Failed to update book'], 500);
        }
    }
    public function orderBook(Request $request)
    {
        $book_id = $request->query('book_id');
        $book = Book::find($book_id);

        if ($book == null) {
            return response()->json(['result' => 'Book not found'], 404);
        }

        if ($book->status == 'Available') {


            $order = new OrderBook();
            $order->book_id = $book->id;
            $order->user_id = $request->user()->id;
            $order->save();

            return response()->json(['result' => 'Book ordered successfully']);
        } else {
            return response()->json(['result' => 'Book not available'], 404);
        }
    }

}



