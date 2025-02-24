<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\QueryBuilder\Concerns\HasConstraints;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\QueryBuilder\Forms\Components\RuleBuilder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Testing\Assert;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method HasConstraints instance()
 *
 * @mixin Testable
 */
class TestsQueryBuilders
{
    public function queryBuilderTable(): Closure
    {
        return function (string $column, string $operatorName, $data = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableConstraintExists($column);

            // $filter = $this->instance()->getTable()->getFilter($name);
            $constraint = $this->instance()->getTable()->getFilter('queryBuilder')->getConstraint($column);
            // dd($constraint);
            if ($constraint instanceof TextConstraint) {
                // $constraint->settings(['text' => $data])
                //     ->inverse(false);
                // $operator = $constraint->getOperator($operatorName);
                // // set
                // $operator
                //     ->constraint($constraint)
                //     ->settings(['text' => $data])
                //     ->inverse(false);

                // $operator->getSummary();

                // if ($data === true || ($data === null && func_num_args() === 1)) {
                //     $data = ['value' => true];
                // } else {
                //     $data = ['value' => $data];
                // }
                $queryBuilder = [
                    'type' => $column,
                    'data' => [
                        'operator' => $operatorName,
                        'settings' => [],
                    ]
                ];

                if ($data) {
                    $queryBuilder['data']['settings'] = ['text' => $data];
                }
            }
            // elseif ($filter instanceof SelectFilter) {
            //     if ($filter->isMultiple()) {
            //         $data = ['values' => array_map(
            //             fn ($record) => $record instanceof Model ? $record->getKey() : $record,
            //             Arr::wrap($data ?? []),
            //         )];
            //     } else {
            //         $data = ['value' => $data instanceof Model ? $data->getKey() : $data];
            //     }
            // } elseif (! is_array($data)) {
            //     $data = ['isActive' => $data === true || $data === null];
            // }
            // dump($this->get("tableFilters"));
            $this->set("tableFilters.queryBuilder.rules.{$constraint->getName()}", $queryBuilder);
            // dump($this->get("tableFilters"));

            return $this;
        };
    }

    // public function parseConstraintsName(string $context)
    // {
    //     return $this->instance()->getConstraint($context);
    // }

    // public function resetTableFilters(): Closure
    // {
    //     return function (): static {
    //         $this->call('resetTableFiltersForm');

    //         return $this;
    //     };
    // }

    // public function removeTableFilter(): Closure
    // {
    //     return function (string $filter, ?string $field = null): static {
    //         $this->call('removeTableFilter', $this->instance()->parseTableFilterName($filter), $field);

    //         return $this;
    //     };
    // }

    // public function removeTableFilters(): Closure
    // {
    //     return function (): static {
    //         $this->call('removeTableFilters');

    //         return $this;
    //     };
    // }

    public function assertTableConstraintExists(): Closure
    {
        return function (string $column): static {
            $filter = $this->instance()->getTable()->getFilter('queryBuilder')->getConstraint($column);

            $livewireClass = $this->instance()::class;

            Assert::assertInstanceOf(
                Constraint::class,
                $filter,
                message: "Failed asserting that a table filter with name [{$column}] exists on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    // public function assertTableFilterVisible(): Closure
    // {
    //     return function (string $name): static {
    //         $name = $this->instance()->parseTableFilterName($name);

    //         $filter = $this->instance()->getTable()->getFilter(
    //             name: $name,
    //             withHidden: true,
    //         );

    //         $livewireClass = $this->instance()::class;

    //         Assert::assertTrue(
    //             $filter->isVisible(),
    //             message: "Failed asserting that a table filter with name [{$name}] is visible on the [{$livewireClass}] component."
    //         );

    //         return $this;
    //     };
    // }

    // public function assertTableFilterHidden(): Closure
    // {
    //     return function (string $name): static {
    //         $name = $this->instance()->parseTableFilterName($name);

    //         $filter = $this->instance()->getTable()->getFilter(
    //             name: $name,
    //             withHidden: true,
    //         );

    //         $livewireClass = $this->instance()::class;

    //         Assert::assertTrue(
    //             $filter->isHidden(),
    //             message: "Failed asserting that a table filter with name [{$name}] is hidden on the [{$livewireClass}] component."
    //         );

    //         return $this;
    //     };
    // }
}
