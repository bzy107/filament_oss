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

it('can filter records by text constraint in the query builder', function (Collection $all, Collection $canSee, Collection $canNotSee, string $column, string $operatorName, string $filter = null) {
    livewire(Posts2Table::class)
        ->assertCanSeeTableRecords($all)
        ->queryBuilderTable($column, $operatorName, $filter)
        ->assertCanSeeTableRecords($canSee)
        ->assertCanNotSeeTableRecords($canNotSee);
})
->with([
    'contains' => function () {
        $posts = Post::factory()->count(10)->create();
        $content = $posts->random()->content;
        $filter = substr($content, 2, 7);
        return [
            'all' => $posts,
            'canSee' => Post::where('content', 'like', '%'. $filter . '%')->get(),
            'canNotSee' => Post::where('content', 'not like', '%'. $filter . '%')->get(),
            'column' => 'content',
            'operatorName' => 'contains',
            'filter' => $filter,
        ];
    },
    'not contains' => function () {
        $posts = Post::factory()->count(10)->create();
        $content = $posts->random()->content;
        $filter = substr($content, 2, 7);
        return [
            'all' => $posts,
            'canSee' => Post::where('content', 'not like', '%' . $filter . '%')->get(),
            'canNotSee' => Post::where('content', 'like' , '%' . $filter . '%')->get(),
            'column' => 'content',
            'operatorName' => 'contains.inverse',
            'filter' => $filter,
        ];
    },
    'startsWith' => function () {
        $posts = Post::factory()->count(10)->create();
        $content = $posts->random()->content;
        $filter = substr($content, 0, 5);
        return [
            'all' => $posts,
            'canSee' => Post::where('content', 'like', $filter . '%')->get(),
            'canNotSee' => Post::where('content', 'not like', $filter . '%')->get(),
            'column' => 'content',
            'operatorName' => 'startsWith',
            'filter' => $filter,
        ];
    },
    'not startsWith' => function () {
        $posts = Post::factory()->count(10)->create();
        $content = $posts->random()->content;
        $filter = substr($content, 0, 5);
        return [
            'all' => $posts,
            'canSee' => Post::where('content', 'not like', $filter . '%')->get(),
            'canNotSee' => Post::where('content', 'like', $filter . '%')->get(),
            'column' => 'content',
            'operatorName' => 'startsWith.inverse',
            'filter' => $filter,
        ];
    },
    'endsWith' => function () {
        $posts = Post::factory()->count(10)->create();
        $content = $posts->random()->content;
        $filter = substr($content, -5);
        return [
            'all' => $posts,
            'canSee' => Post::where('content', 'like', '%' . $filter)->get(),
            'canNotSee' => Post::where('content', 'not like', '%' . $filter)->get(),
            'column' => 'content',
            'operatorName' => 'endsWith',
            'filter' => $filter,
        ];
    },
    'not endsWith' => function () {
        $posts = Post::factory()->count(10)->create();
        $content = $posts->random()->content;
        $filter = substr($content, -5);
        return [
            'all' => $posts,
            'canSee' => Post::where('content', 'not like', '%' . $filter)->get(),
            'canNotSee' => Post::where('content', 'like', '%' . $filter)->get(),
            'column' => 'content',
            'operatorName' => 'endsWith.inverse',
            'filter' => $filter,
        ];
    },
    'equals' => function () {
        $posts = Post::factory()->count(10)->create();
        $content = $posts->random()->content;
        return [
            'all' => $posts,
            'canSee' => Post::where('content', $content)->get(),
            'canNotSee' => Post::where('content', '<>', $content)->get(),
            'column' => 'content',
            'operatorName' => 'equals',
            'filter' => $content,
        ];
    },
    'not equals' => function () {
        $posts = Post::factory()->count(10)->create();
        $content = $posts->random()->content;
        return [
            'all' => $posts,
            'canSee' => Post::where('content', '<>', $content)->get(),
            'canNotSee' => Post::where('content', $content)->get(),
            'column' => 'content',
            'operatorName' => 'equals.inverse',
            'filter' => $content,
        ];
    },
    'isFilled' => function () {
        Post::factory()->count(8)->create();
        Post::factory()->create(['content' => null]);
        Post::factory()->create(['content' => '']);
        return [
            'all' => Post::all(),
            'canSee' => Post::where('content', '<>', null)->where('content', '<>', '')->get(),
            'canNotSee' => Post::where('content', null)->orWhere('content', '')->get(),
            'column' => 'content',
            'operatorName' => 'isFilled',
        ];
    },
    'not isFilled' => function () {
        Post::factory()->count(8)->create();
        Post::factory()->create(['content' => null]);
        Post::factory()->create(['content' => '']);
        return [
            'all' => Post::all(),
            'canSee' => Post::where('content', null)->orWhere('content', '')->get(),
            'canNotSee' => Post::where('content', '<>', null)->where('content', '<>', '')->get(),
            'column' => 'content',
            'operatorName' => 'isFilled.inverse',
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
