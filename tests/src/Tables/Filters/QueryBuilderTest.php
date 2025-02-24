<?php

use Filament\Tables\Filters\Filter;
use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\Posts2Table;
use Filament\Tests\Tables\Fixtures\PostsTable;

use Filament\Tests\Tables\TestCase;
use function Filament\Tests\livewire;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

uses(TestCase::class);

it('can filter records by text constraint in the query builder', function (Collection $all, Collection $canSee, Collection $canNotSee, string $column, string $operatorName, string $filter) {
    // $posts = Post::factory()->count(10)->create();
    // $title = $posts->first()->title;

    livewire(Posts2Table::class)
        ->assertCanSeeTableRecords($all)
        ->queryBuilderTable($column, $operatorName, $filter)
        ->assertCanSeeTableRecords($canSee)
        ->assertCanNotSeeTableRecords($canNotSee);
})
->with([
    'contains' => function () {
        $posts = Post::factory()->count(10)->create();
        $title = $posts->random()->title;
        $filter = substr($title, 2, 7);
        return [
            'all' => $posts,
            'canSee' => Post::where('title', 'like', '%'. $filter . '%')->get(),
            'canNotSee' => Post::where('title', 'not like', '%'. $filter . '%')->get(),
            'column' => 'title',
            'operatorName' => 'contains',
            'filter' => $filter,
        ];
    },
    'not contains' => function () {
        $posts = Post::factory()->count(10)->create();
        $title = $posts->random()->title;
        $filter = substr($title, 2, 7);
        return [
            'all' => $posts,
            'canSee' => Post::where('title', 'not like', '%' . $filter . '%')->get(),
            'canNotSee' => Post::where('title', 'like' , '%' . $filter . '%')->get(),
            'column' => 'title',
            'operatorName' => 'contains.inverse',
            'filter' => $filter,
        ];
    },
    'startsWith' => function () {
        $posts = Post::factory()->count(10)->create();
        $title = $posts->random()->title;
        $filter = substr($title, 0, 5);
        return [
            'all' => $posts,
            'canSee' => Post::where('title', 'like', $filter . '%')->get(),
            'canNotSee' => Post::where('title', 'not like', $filter . '%')->get(),
            'column' => 'title',
            'operatorName' => 'startsWith',
            'filter' => $filter,
        ];
    },
    'not startsWith' => function () {
        $posts = Post::factory()->count(10)->create();
        $title = $posts->random()->title;
        $filter = substr($title, 0, 5);
        return [
            'all' => $posts,
            'canSee' => Post::where('title', 'not like', $filter . '%')->get(),
            'canNotSee' => Post::where('title', 'like', $filter . '%')->get(),
            'column' => 'title',
            'operatorName' => 'startsWith.inverse',
            'filter' => $filter,
        ];
    },
    'endsWith' => function () {
        $posts = Post::factory()->count(10)->create();
        $title = $posts->random()->title;
        $filter = substr($title, -5);
        return [
            'all' => $posts,
            'canSee' => Post::where('title', 'like', '%' . $filter)->get(),
            'canNotSee' => Post::where('title', 'not like', '%' . $filter)->get(),
            'column' => 'title',
            'operatorName' => 'endsWith',
            'filter' => $filter,
        ];
    },
    'not endsWith' => function () {
        $posts = Post::factory()->count(10)->create();
        $title = $posts->random()->title;
        $filter = substr($title, -5);
        return [
            'all' => $posts,
            'canSee' => Post::where('title', 'not like', '%' . $filter)->get(),
            'canNotSee' => Post::where('title', 'like', '%' . $filter)->get(),
            'column' => 'title',
            'operatorName' => 'endsWith.inverse',
            'filter' => $filter,
        ];
    },
    'equals' => function () {
        $posts = Post::factory()->count(10)->create();
        $title = $posts->random()->title;
        return [
            'all' => $posts,
            'canSee' => Post::where('title', $title)->get(),
            'canNotSee' => Post::where('title', '<>', $title)->get(),
            'column' => 'title',
            'operatorName' => 'equals',
            'filter' => $title,
        ];
    },
    'not equals' => function () {
        $posts = Post::factory()->count(10)->create();
        $title = $posts->random()->title;
        return [
            'all' => $posts,
            'canSee' => Post::where('title', '<>', $title)->get(),
            'canNotSee' => Post::where('title', $title)->get(),
            'column' => 'title',
            'operatorName' => 'equals.inverse',
            'filter' => $title,
        ];
    },
    'isFilled' => function () {
        Post::factory()->count(9)->create();
        Post::factory()->create(['content' => null]);
        dd(Post::all());
        return [
            'all' => Post::all(),
            'canSee' => Post::where('content', '<>', null)->get(),
            'canNotSee' => Post::where('content', null)->get(),
            'column' => 'content',
            'operatorName' => 'isFilled',
            'filter' => null,
        ];
    },
    'not isFilled' => function () {
        Post::factory()->count(9)->create();
        Post::factory()->create(['content' => null]);
        return [
            'all' => Post::all(),
            'canSee' => Post::where('content', null)->get(),
            'canNotSee' => Post::where('content', '<>', null)->get(),
            'column' => 'content',
            'operatorName' => 'isFilled.inverse',
            'filter' => null,
        ];
    },
]);

// it('can filter records by relationship', function () {
//     $posts = Post::factory()->count(10)->create();

//     $author = $posts->first()->author;

//     livewire(PostsTable::class)
//         ->assertCanSeeTableRecords($posts)
//         ->filterTable('author', $author)
//         ->assertCanSeeTableRecords($posts->where('author_id', $author->getKey()))
//         ->assertCanNotSeeTableRecords($posts->where('author_id', '!=', $author->getKey()));
// });

// it('can persist filters in the user\'s session', function () {
//     $posts = Post::factory()->count(10)->create();

//     $unpublishedPosts = $posts->where('is_published', false);

//     livewire(PostsTable::class)
//         ->assertCanSeeTableRecords($posts)
//         ->filterTable('is_published')
//         ->assertCanNotSeeTableRecords($unpublishedPosts);

//     livewire(PostsTable::class)
//         ->assertCanNotSeeTableRecords($unpublishedPosts);

//     livewire(PostsTable::class)
//         ->resetTableFilters()
//         ->assertCanSeeTableRecords($unpublishedPosts);

//     livewire(PostsTable::class)
//         ->assertCanSeeTableRecords($unpublishedPosts);
// });

// it('can reset filters', function () {
//     $posts = Post::factory()->count(10)->create();

//     $unpublishedPosts = $posts->where('is_published', false);

//     livewire(PostsTable::class)
//         ->filterTable('is_published')
//         ->assertCanNotSeeTableRecords($unpublishedPosts)
//         ->resetTableFilters()
//         ->assertCanSeeTableRecords($unpublishedPosts);
// });

// it('can remove a filter', function () {
//     $posts = Post::factory()->count(10)->create();

//     $unpublishedPosts = $posts->where('is_published', false);

//     livewire(PostsTable::class)
//         ->assertCanSeeTableRecords($posts)
//         ->filterTable('is_published')
//         ->assertCanNotSeeTableRecords($unpublishedPosts)
//         ->removeTableFilter('is_published')
//         ->assertCanSeeTableRecords($posts);
// });

// it('can remove all table filters', function () {
//     $posts = Post::factory()->count(10)->create();

//     $unpublishedPosts = $posts->where('is_published', false);

//     livewire(PostsTable::class)
//         ->assertCanSeeTableRecords($posts)
//         ->filterTable('is_published')
//         ->assertCanNotSeeTableRecords($unpublishedPosts)
//         ->removeTableFilters()
//         ->assertCanSeeTableRecords($posts);
// });

// it('can use a custom attribute for the `SelectFilter`', function () {
//     $posts = Post::factory()->count(10)->create();

//     $unpublishedPosts = $posts->where('is_published', false);

//     livewire(PostsTable::class)
//         ->assertCanSeeTableRecords($posts)
//         ->filterTable('select_filter_attribute', false)
//         ->assertCanSeeTableRecords($unpublishedPosts)
//         ->filterTable('select_filter_attribute', true)
//         ->assertCanNotSeeTableRecords($unpublishedPosts);
// });

// it('can assert a filter exists with a given configuration', function () {
//     livewire(PostsTable::class)
//         ->assertTableFilterExists('is_published', function (Filter $filter): bool {
//             return $filter->getLabel() === 'Is published';
//         });
// });

// it('can check if a filter is visible', function (): void {
//     livewire(PostsTable::class)
//         ->assertTableFilterVisible('is_published');
// });

// it('can check if a filter is hidden', function (): void {
//     livewire(PostsTable::class)
//         ->assertTableFilterHidden('hidden_filter');
// });
