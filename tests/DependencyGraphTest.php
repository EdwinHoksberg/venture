<?php declare(strict_types=1);

use Stubs\TestJob1;
use Stubs\TestJob2;
use Stubs\TestJob3;
use function PHPUnit\Framework\assertEquals;
use Sassnowski\Venture\Graph\DependencyGraph;

beforeEach(function () {
    $this->graph = new DependencyGraph();
});

it('returns no dependencies if a job has none', function () {
    $job = new TestJob1();

    $this->graph->addDependantJob($job, []);

    assertEquals([], $this->graph->getDependencies($job));
});

it('returns the jobs direct dependencies', function () {
    $job = new TestJob1();

    $this->graph->addDependantJob($job, ['::dependency-1::', '::dependency-2::']);

    assertEquals(['::dependency-1::', '::dependency-2::'], $this->graph->getDependencies($job));
});

it('returns the instances of all dependants of a job', function () {
    $job1 = new TestJob1();
    $job2 = new TestJob2();
    $job3 = new TestJob3();

    $this->graph->addDependantJob($job1, [TestJob2::class]);
    $this->graph->addDependantJob($job3, [TestJob2::class]);

    assertEquals([$job1, $job3], $this->graph->getDependantJobs($job2));
});

it('returns all jobs without dependencies', function () {
    $job1 = new TestJob1();
    $job2 = new TestJob2();

    $this->graph->addDependantJob($job1, []);
    $this->graph->addDependantJob($job2, [TestJob1::class]);

    assertEquals([$job1], $this->graph->getJobsWithoutDependencies());
});

it('returns an empty array if there are no jobs with unresolvable dependencies', function () {
    $this->graph->addDependantJob(new TestJob1(), []);
    $this->graph->addDependantJob(new TestJob2(), []);

    assertEquals([], $this->graph->getUnresolvableDependencies());
});

it('returns a list of unresolvable dependencies and their dependants', function () {
    $this->graph->addDependantJob(new TestJob2(), [TestJob1::class]);

    assertEquals([
        TestJob1::class => [TestJob2::class],
    ], $this->graph->getUnresolvableDependencies());
});

it('can register jobs with dependencies before their dependencies are registered', function () {
    $this->graph->addDependantJob(new TestJob2(), [TestJob1::class]);
    $this->graph->addDependantJob(new TestJob1(), []);

    assertEquals([], $this->graph->getUnresolvableDependencies());
});
