<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tribe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StaffController extends Controller
{
    public function dashboard()
    {
        $stats = DB::select('SELECT * FROM staff_stat');

        $users = User::with('role')->get();
        $roles = DB::table('roles')->get();

        return Inertia::render('Staff', [
            'stats' => $stats,
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function display_info()
    {
        $users = User::with('role')->get();
        $roles = DB::table('roles')->get();

        return Inertia::render('Staff', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function storeTribe(Request $request)
    {
        DB::statement("SET app.current_user_id = " . auth()->id());
        // Validate the request data
        $validated = $request->validate([
            'tribe_name' => 'required|string|max:255',
            'description' => 'required|string',
            'language' => 'required|string',
            'cultural_practices' => 'required|string',
            'population' => 'required|string',
            'region' => 'required|string',
            'url_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        // Get authenticated user ID
        $userId = Auth::id();

        // Handle image upload
        $imagePath = $request->file('url_image')
            ? $request->file('url_image')->store('images', 'public')
            : null;

        // Log the image path to check if it's being uploaded
        \Log::info('Image path: ' . $imagePath);

        // Insert the tribe into the database
        DB::table('tribes')->insert([
            'tribe_name' => $validated['tribe_name'],
            'description' => $validated['description'],
            'language' => $validated['language'],
            'cultural_practices' => $validated['cultural_practices'],
            'population' => $validated['population'],
            'region' => $validated['region'],
            'url_image' => $imagePath,
            'category_id' => $validated['category_id'],
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return back()->with('message', 'Tribe added successfully');
        //     return redirect()->route('staff.dashboard')->with('success', 'Tribe added successfully!');
        // } catch (\Illuminate\Validation\ValidationException $e) {
        //     return redirect()->route('staff.dashboard')
        //         ->withErrors($e->errors())
        //         ->withInput();
        // } catch (\Exception $e) {
        //     \Log::error('Failed to store tribe: ' . $e->getMessage());
        //     return redirect()->route('staff.dashboard')->with('error', 'Failed to add the tribe. Please try again.');
        // }

    }



    public function updateTribe(Request $request, $id)
    {
        DB::statement("SET app.current_user_id = " . auth()->id());

        // Find the tribe by ID using DB
        $tribe = DB::table('tribes')->where('id', $id)->first();

        // Check if the tribe exists
        if (!$tribe) {
            return redirect()->route('staff.dashboard')->with('error', 'Tribe not found.');
        }

        // Check if the user is authorized to update the tribe
        if ($tribe->user_id !== Auth::id()) {
            return redirect()->route('staff.dashboard')->with('error', 'You are not authorized to edit this tribe.');
        }


        // Validate the request data
        $validated = $request->validate([
            'tribe_name' => 'required|string|max:255',
            'description' => 'required|string',
            'language' => 'required|string',
            'cultural_practices' => 'required|string',
            'population' => 'required|string',
            'region' => 'required|string',
            'url_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        // Handle image upload
        $imagePath = $request->file('url_image')
            ? $request->file('url_image')->store('images', 'public')
            : $tribe->url_image; // Retain the existing image if none is uploaded

        // Log the image path for debugging
        \Log::info('Updated image path: ' . $imagePath);

        // Update the tribe in the database using DB
        DB::table('tribes')->where('id', $id)->update([
            'tribe_name' => $validated['tribe_name'],
            'description' => $validated['description'],
            'language' => $validated['language'],
            'cultural_practices' => $validated['cultural_practices'],
            'population' => $validated['population'],
            'region' => $validated['region'],
            'url_image' => $imagePath,
            'category_id' => $validated['category_id'],
            'updated_at' => now(),
        ]);
        return back()->with('message', 'Tribe updated successfully!');
        //     return redirect()->route('staff.dashboard')->with('success', 'Tribe updated successfully!');
        // } catch (\Illuminate\Validation\ValidationException $e) {
        //     return redirect()->route('staff.dashboard')
        //         ->withErrors($e->errors())
        //         ->withInput();
        // } catch (\Exception $e) {
        //     \Log::error('Failed to update tribe: ' . $e->getMessage());
        //     return redirect()->route('staff.dashboard')->with('error', 'Failed to update the tribe. Please try again.');
        // }
    }




    public function editTribe($id)
    {
        $tribe = Tribe::where('user_id', Auth::id())->findOrFail($id);
        $categories = Category::all();

        return Inertia::render('staff/editTribe', [
            'tribe' => $tribe,
            'categories' => $categories,
        ]);
    }

    public function deleteTribe($id)
    {

        DB::statement("SET app.current_user_id = " . auth()->id());

        // Find the tribe by ID using DB
        $tribe = DB::table('tribes')->where('id', $id)->first();

        // Check if the tribe exists
        if (!$tribe) {
            return redirect()->route('staff.dashboard')->with('error', 'Tribe not found.');
        }

        // Check if the user is authorized to delete the tribe
        if ($tribe->user_id !== Auth::id()) {
            return redirect()->route('staff.dashboard')->with('error', 'You are not authorized to delete this tribe.');
        }


        // Delete the tribe using DB
        DB::table('tribes')->where('id', $id)->delete();


        return back()->with('message', 'Tribe deleted successfully!');
        //     return redirect()->route('staff.dashboard')->with('success', 'Tribe deleted successfully!');
        // } catch (\Exception $e) {
        //     \Log::error('Failed to delete tribe: ' . $e->getMessage());
        //     return redirect()->route('staff.dashboard')->with('error', 'Failed to delete the tribe. Please try again.');
        // }
    }

    public function getDashboardStats()
    {
        $userId = Auth::id();
        $totalTribes = Tribe::where('user_id', $userId)->count();
        $totalCategories = Category::count();
        $recentTribes = Tribe::where('user_id', $userId)->latest()->take(5)->get();
        $categories = Category::all();
        $tribes = Tribe::where('user_id', $userId)->with('category')->get();

        return response()->json([
            'totalTribes' => $totalTribes,
            'totalTribesCreated' => $totalTribes,
            'totalCategories' => $totalCategories,
            'recentTribes' => $recentTribes,
            'categories' => $categories,
            'tribes' => $tribes,
        ]);
    }
}
