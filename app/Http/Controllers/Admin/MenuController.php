<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class MenuController extends Controller
{
	public function getIndex()
	{	
		$items 	= Menu::orderBy('order')->get();
		$menu 	= new Menu;
		$menu   = $menu->getHTML($items);
		return view('admin.menu.builder',compact('items','menu'));
	}
	// Create new Menu
	public function postNew()
	{
		// Create a new menu item and save it
		$item = new Menu;
		$item->title 	= e(Input::get('title','untitled'));
		$item->label 	= e(Input::get('label',''));	
		$item->url 		= e(Input::get('url',''));	
		$item->order 	= Menu::max('order')+1;
		$item->save();
		return redirect('admin/menu');
	}
	// Edit Menu
	public function getEdit($id)
	{	
		$item = Menu::find($id);
		return view('admin.menu.edit',compact('item'));
	}
	// Update Menu
	public function postEdit($id)
	{	
		$item = Menu::find($id);
		$item->title 	= e(Input::get('title','untitled'));
		$item->label 	= e(Input::get('label',''));	
		$item->url 		= e(Input::get('url',''));	
		$item->save();
		return redirect("admin/menu/edit/{$id}");
	}
	// AJAX Reordering function
	public function postIndex()
	{	
	    $source       = e(Input::get('source'));
	    $destination  = e(Input::get('destination',0));
	    // return ($source);
	    // return ($destination);	     
	    $item             = Menu::find($source);
	    if(is_null($destination) || $destination ==''){
	    	$item->parent_id  = 0;  
	    } else {
	    	$item->parent_id  = $destination;  
	    }
	    $item->save();

	    $ordering       = json_decode(Input::get('order'));
	    $rootOrdering   = json_decode(Input::get('rootOrder'));

	    if($ordering){
	      foreach($ordering as $order=>$item_id){
	        if($itemToOrder = Menu::find($item_id)){
	            $itemToOrder->order = $order;
	            $itemToOrder->save();
	        }
	      }
	    } else {
	      foreach($rootOrdering as $order=>$item_id){
	        if($itemToOrder = Menu::find($item_id)){
	            $itemToOrder->order = $order;
	            $itemToOrder->save();
	        }
	      }
	    }
	    return 'ok ';
	}
	// delete menu
	public function postDelete()
	{
		$id = Input::get('delete_id');
		// Find all items with the parent_id of this one and reset the parent_id to zero
		$items = Menu::where('parent_id', $id)->get()->each(function($item)
		{
			$item->parent_id = 0;  
			$item->save();  
		});
		// Find and delete the item that the user requested to be deleted
		$item = Menu::find($id);
		$item->delete();
		return redirect('admin/menu');
	}
}
