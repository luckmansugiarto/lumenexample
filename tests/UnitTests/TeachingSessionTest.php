<?php

namespace UnitTests;

use App\Models\Book;
use App\Models\TeachingSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Testing\TestCase;

class TeachingSessionTest extends TestCase
{
    use DatabaseTransactions;

    private $dateFormat = 'Y-m-d H:i:s';

    public function setUp(): void
    {
        parent::setUp();
        $this->withExceptionHandling();
    }

    public function test_unauthenticated_access()
    {
        $this->get('/sessions');

        $this->response->assertJson([]);
    }

    public function test_unauthenticated_access_to_sessions()
    {
        $sessions = TeachingSession::factory()->count(2)->create();
        $sessions = $sessions->sortBy('start_time');
        $this->get('/sessions');

        $this->response->assertUnauthorized();
    }

    public function test_authenticated_access()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/sessions');
        $this->response->assertStatus(Response::HTTP_OK);

        return $user;
    }

    /**
     * @depends test_authenticated_access
     */
    public function test_user_with_no_session($user)
    {
        $this->actingAs($user)->get('/sessions');

        $this->response->assertJsonCount(0, '*.session_id');
    }

    /**
     * @depends test_authenticated_access
     */
    public function test_user_having_only_one_past_session($user)
    {
        $session = TeachingSession::factory()
            ->forUser(['id' => $user->id])
            ->create([
                'start_time' => Carbon::now()->subDays(2),
                'end_time' => Carbon::now()->subDay()
            ]);

        $this->actingAs($user)->get('/sessions');

        $this->response->assertJsonCount(0, '*.session_id');
    }

    /**
     * @depends test_authenticated_access
     */
    public function test_user_having_only_one_future_session($user)
    {
        $session = TeachingSession::factory()
            ->forUser(['id' => $user->id])
            ->create([
                'start_time' => Carbon::now()->addDay()
            ]);

        $this->actingAs($user)->get('/sessions');

        $this->response->assertExactJson($this->getExpectedOutputs([$session]));
    }

    public function test_user_having_more_than_one_future_sessions()
    {
        $sessions = [];

        $user = User::factory()->create();

        for ($count = 0; $count < 3; $count++)
        {
            $startDay = 3 - $count;
            $sessions[] = TeachingSession::factory()
                ->for($user)
                ->create([
                    'start_time' => Carbon::now()->addDays($startDay),
                    'end_time' => Carbon::now()->addDays($startDay + 1)
                ]);
        }

        $sessions = collect($sessions)->sortBy('start_time');

        $this->actingAs($user)->get('/sessions');

        $this->response->assertExactJson($this->getExpectedOutputs($sessions));
    }

    public function test_user_having_both_past_and_future_sessions()
    {
        $user = User::factory()->create();

        $validSession = TeachingSession::factory()
            ->for($user)
            ->create([
                'start_time' => Carbon::now()->addDay(),
                'end_time' => Carbon::now()->addDays(2)
            ]);

        TeachingSession::factory()
            ->for($user)
            ->create([
                'start_time' => Carbon::now()->subDays(2),
                'end_time' => Carbon::now()->addDay()
            ]);

        $this->actingAs($user)->get('/sessions');

        $this->response->assertExactJson($this->getExpectedOutputs(collect([$validSession])));
    }

    public function test_user_having_both_past_and_future_sessions_with_show_past_sessions_disabled()
    {
        $user = User::factory()->create();

        $validSession = TeachingSession::factory()
            ->for($user)
            ->create([
                'start_time' => Carbon::now()->addDay(),
                'end_time' => Carbon::now()->addDays(2)
            ]);

        TeachingSession::factory()
            ->for($user)
            ->create([
                'start_time' => Carbon::now()->subDays(2),
                'end_time' => Carbon::now()->addDay()
            ]);

        $this->actingAs($user)->get('/sessions?show_past=0');

        $this->response->assertExactJson($this->getExpectedOutputs(collect([$validSession])));
    }

    public function test_user_having_both_past_and_future_sessions_with_show_past_sessions_enabled()
    {
        $user = User::factory()->create();
        $validSessions = [];

        $validSessions[] = TeachingSession::factory()
            ->for($user)
            ->create([
                'start_time' => Carbon::now()->addDay(),
                'end_time' => Carbon::now()->addDays(2)
            ]);

        $validSessions[] = TeachingSession::factory()
            ->for($user)
            ->create([
                'start_time' => Carbon::now()->subDays(2),
                'end_time' => Carbon::now()->addDay()
            ]);

        $this->actingAs($user)->get('/sessions?show_past=1');

        $validSessions = collect($validSessions)->sortBy('start_time');

        $this->response->assertExactJson($this->getExpectedOutputs($validSessions));
    }

    public function test_get_list_of_future_sessions_belonging_to_user_only()
    {
        $users = User::factory()->count(2)->create();

        $validSessions = TeachingSession::factory()
            ->for($users[0])
            ->count(2)
            ->create([
                'start_time' => Carbon::now()->addDay(),
                'end_time' => Carbon::now()->addDays(2)
            ]);

        TeachingSession::factory()
            ->for($users[1])
            ->count(2)
            ->create([
                'start_time' => Carbon::now()->addDay(),
                'end_time' => Carbon::now()->addDays(2)
            ]);

        $validSessions = collect($validSessions)->sortBy('start_time');

        $this->actingAs($users[0])->get('/sessions');

        $this->response->assertExactJson($this->getExpectedOutputs($validSessions));
    }

    /**
     * @depends test_authenticated_access
     */
    public function test_user_get_valid_future_session_by_id($user)
    {
        $session = TeachingSession::factory()
            ->forUser(['id' => $user->id])
            ->create([
                'start_time' => Carbon::now()->addDay(),
                'end_time' => Carbon::now()->addDays(2)
            ]);

        $session = $session->loadMissing('books');

        $this->actingAs($user)->get('/session/' . $session->id);

        $this->response->assertExactJson($this->getExpectedOutputs([$session], true));
    }

    /**
     * @depends test_authenticated_access
     */
    public function test_user_cannot_access_other_user_session($user)
    {
        $anotherUser = User::factory()->create();

        $session = TeachingSession::factory()
            ->forUser(['id' => $user->id])
            ->create();

        $this->actingAs($anotherUser)->get('/session/' . $session->id);

        $this->response->assertNotFound();
    }

    /**
     * @depends test_authenticated_access
     */
    public function test_unauthenticated_user_adding_a_new_session($user)
    {
        $this->post('/session', [
            'name' => 'Test session',
            'start_date' => Carbon::now()->addDay()->format($this->dateFormat),
            'end_date' => Carbon::now()->addDays(2)->format($this->dateFormat),
            'user' => $user->id
        ]);

        $this->response->assertUnauthorized();
    }

    public function test_authenticated_user_adding_a_new_session()
    {
        $user = User::factory()->create();

        $newSession = [
            'name' => 'Test session',
            'start_date' => Carbon::now()->addDay()->format($this->dateFormat),
            'end_date' => Carbon::now()->addDays(2)->format($this->dateFormat)
        ];

        $this->actingAs($user)->post('/session', $newSession);

        $this->response->assertCreated();

        $this->seeInDatabase('teaching_sessions', [
            'session_name' => $newSession['name'],
            'user_id' => $user->id,
            'start_time' => $newSession['start_date'],
            'end_time' => $newSession['end_date']
        ]);
    }

    public function test_unauthenticated_user_adding_a_new_book_to_existing_session()
    {
        $session = TeachingSession::factory()->create();

        $book = Book::factory()->create();

        $this->put('/session/' . $session->id . '/book/' . $book->id);

        $this->response->assertUnauthorized();
    }

    /**
     * @depends test_authenticated_access
     */
    public function test_add_a_new_book_to_existing_session($user)
    {
        $session = TeachingSession::factory()
            ->forUser(['id' => $user->id])
            ->create();

        $book = Book::factory()->create();

        $this->actingAs($user)->put('/session/' . $session->id . '/book/' . $book->id);

        $this->response->assertCreated();
    }

    public function test_delete_an_existing_session_with_unauthenticated_access()
    {
        $session = TeachingSession::factory()->create();

        $this->delete('/session/' . $session->id);

        $this->response->assertUnauthorized();
    }

    /**
     * @depends test_authenticated_access
     */
    public function test_remove_a_book_from_an_existing_session_by_unauthorized_user($user)
    {
        $session = TeachingSession::factory()
            ->forUser(['id' => $user->id])
            ->hasBooks(2)
            ->create();

        $anotherUser = User::factory()->create();

        $this->actingAs($anotherUser)->delete('/session/' . $session->id . '/book/' . $session->books[0]->id);

        $this->response->assertNotFound();
    }

    /**
     * @depends test_authenticated_access
     */
    public function test_remove_a_book_from_an_existing_session_by_authorized_user($user)
    {
        $session = TeachingSession::factory()
            ->forUser(['id' => $user->id])
            ->hasBooks(2)
            ->create();

        $anotherUser = User::factory()->create();

        $this->actingAs($anotherUser)->delete('/session/' . $session->id . '/book/' . $session->books[0]->id);

        $this->response->assertNotFound();
    }

    /**
     * @depends test_authenticated_access
     */
    public function test_remove_an_existing_session_with_books_by_unauthorized_user($user)
    {
        $session = TeachingSession::factory()
            ->forUser(['id' => $user->id])
            ->hasBooks(2)
            ->create();

        $anotherUser = User::factory()->create();

        $this->actingAs($anotherUser)->delete('/session/' . $session->id);

        $this->response->assertNotFound();
    }

    /**
     * @depends test_authenticated_access
     */
    public function test_remove_an_existing_session_with_no_books_by_authorized_user($user)
    {
        $session = TeachingSession::factory()
            ->forUser(['id' => $user->id])
            ->create();

        $this->actingAs($user)->delete('/session/' . $session->id);

        $session->refresh();

        $this->assertTrue($session->trashed(), 'Failed to delete an existing session');
    }

    /**
     * @depends test_authenticated_access
     */
    public function test_remove_an_existing_session_with_books_by_authorized_user($user)
    {
        $session = TeachingSession::factory()
            ->forUser(['id' => $user->id])
            ->hasBooks(2)
            ->create();

        $this->actingAs($user)->delete('/session/' . $session->id);

        $this->response->assertNoContent();

        $books = TeachingSession::withTrashed()
            ->where('id', $session->id)
            ->first()->books;

        $this->assertEquals(0, $books->count(), 'Failed to delete books before deleting a session');
    }

    private function getExpectedOutputs($sessions)
    {
        $outputs = [];

        foreach ($sessions as $session)
        {
            $details = [
                'session_id' => $session->id,
                'session_name' => $session->session_name,
                'start_date' => $session->start_time,
                'end_date' => $session->end_time
            ];

            if ($session->relationLoaded('books'))
            {
                $details['books'] = [];

                foreach ($session->books as $book)
                {
                    $details['books'][] = [
                        'id' => $book->id,
                        'title' => $book->name,
                        'author' => $book->author,
                        'published_date' => $book->published_at,
                        'isbn' => $book->isbn
                    ];
                }
            }

            $outputs[] = $details;
        }

        return $outputs;
    }
}
