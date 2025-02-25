<?php

use Filament\Tables\Actions\DeleteAction;
use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsQueryBuilderTable;
use Filament\Tests\Tables\TestCase;
use function Filament\Tests\livewire;
use function Pest\Laravel\assertSoftDeleted;
use Illuminate\Database\Eloquent\Collection;

uses(TestCase::class);

it('can filter records by text constraint in the query builder', function (Collection $all, Collection $canSee, Collection $canNotSee, string $column, string $operatorName, string $filter = null) {
    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($all)
        ->queryBuilderTable($column, $operatorName, $filter)
        ->assertCanSeeTableRecords($canSee)
        ->assertCanNotSeeTableRecords($canNotSee);
})
->with([
    'contains' => function () {
        $posts = Post::factory(10)->create();
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
        $posts = Post::factory(10)->create();
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
        $posts = Post::factory(10)->create();
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
        $posts = Post::factory(10)->create();
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
        $posts = Post::factory(10)->create();
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
        $posts = Post::factory(10)->create();
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
        $posts = Post::factory(10)->create();
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
        $posts = Post::factory(10)->create();
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
        Post::factory(8)->create();
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
        Post::factory(8)->create();
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

it('can filter records by text constraint in the query builder with modal', function () {
    $posts = Post::factory()->count(10)->create();
    $content = $posts->first()->content;
    $post = Post::where('content', $content);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts)
        ->queryBuilderTable('content', 'contains', $content)
        ->assertCanSeeTableRecords($post->get())
        ->callTableAction(DeleteAction::class, $post->first());

    assertSoftDeleted($post->first());
});
