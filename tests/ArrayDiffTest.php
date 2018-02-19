<?php

use Differ\ArrayDiff;
use PHPUnit\Framework\TestCase;

class ArrayDiffTest extends TestCase
{
    public function testDiffReturnsAddedRemovedChangedKeys()
    {
        $differ = new ArrayDiff();
        $diff = $differ->diff(['a', 'b', 'c'], ['b', 'c', 'd']);

        $this->assertArrayHasKey('added', $diff);
        $this->assertArrayHasKey('removed', $diff);
        $this->assertArrayHasKey('changed', $diff);
    }

    /**
     * @dataProvider addedKeyProvider
     */
    public function testFindsAddedKeys($old, $new, $expectedKey)
    {
        $differ = new ArrayDiff();
        $diff = $differ->diff($old, $new);

        $this->assertArrayHasKey($expectedKey, $diff['added']);
    }

    public function addedKeyProvider()
    {
        return [
            [['a', 'b', 'c'], ['a', 'b', 'c', 'd'], 3],
            [['a' => 1, 'b' => 2, 'c' => 3], ['b' => 2, 'c' => 3, 'd' => 4], 'd'],
            [['a' => 1, 'b' => 2, 'c' => 3], ['b' => 2, 'c' => 3, 'd' => null], 'd'],
            [['a' => 1], ['b' => 2, 'c' => 3, 'd' => null], 'b'],
        ];
    }

    /**
     * @dataProvider nestedAddedKeyProvider
     */
    public function testFindsNestedAddedKeys($old, $new, $expectedKey)
    {
        $differ = new ArrayDiff();
        $diff = $differ->diff($old, $new);

        $this->assertArrayHasKey($expectedKey, $diff['changed']['a']['added']);
    }

    public function nestedAddedKeyProvider()
    {
        return [
            // test nested added numeric key
            [['a' => [1, 2]], ['a' => [1, 2, 3]], 2],
            [['a' => ['one', 'two', 'three']], ['a' => ['one', 'two', 'three', 'four']], 3],
            // test nested added associative key
            [['a' => ['one' => 1, 'two' => 1, 'three' => 1]], ['a' => ['one' => 1, 'two' => 1, 'three' => 1, 'four' => []]], 'four'],
        ];
    }

    /**
     * @dataProvider removedKeyProvider
     */
    public function testFindsRemovedKeys($old, $new, $expectedKey)
    {
        $differ = new ArrayDiff();
        $diff = $differ->diff($old, $new);

        $this->assertArrayHasKey($expectedKey, $diff['removed']);
    }

    public function removedKeyProvider()
    {
        return [
            [['a', 'b', 'c', 'd'], ['a', 'b', 'c'], 3],
            [['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4], ['b' => 2, 'c' => 3], 'd'],
            [['a' => 1, 'b' => 2, 'c' => 3, 'd' => null], ['b' => 2, 'c' => 3], 'd'],
            [['a' => 1, 'b' => 2, 'c' => 3, 'd' => null], ['b' => 2, 'c' => 3], 'a'],
            [['b' => 2, 'c' => 3, 'd' => null], ['a' => 1], 'b'],
            [['c' => 3, 'd' => null], ['a' => 1, 'b' => 2], 'c'],
        ];
    }

    /**
     * @dataProvider nestedRemovedKeyProvider
     */
    public function testFindsNestedRemovedKeys($old, $new, $expectedKey)
    {
        $differ = new ArrayDiff();
        $diff = $differ->diff($old, $new);

        $this->assertArrayHasKey($expectedKey, $diff['changed']['a']['removed']);
    }

    public function nestedRemovedKeyProvider()
    {
        return [
            // test nested removed numeric key
            [['a' => [1, 2, 3]], ['a' => [1, 2]], 2],
            [['a' => ['one', 'two', 'three', 'four']], ['a' => ['one', 'two', 'three']], 3],
            // test nested removed associative key
            [['a' => ['one' => 1, 'two' => 1, 'three' => 1, 'four' => []]], ['a' => ['one' => 1, 'two' => 1, 'three' => 1]], 'four'],
        ];
    }

    /**
     * @dataProvider changedKeyProvider
     */
    public function testFindsChangedKeys($old, $new, $expectedKey)
    {
        $differ = new ArrayDiff();
        $diff = $differ->diff($old, $new);

        $this->assertArrayHasKey($expectedKey, $diff['changed']);
    }

    public function changedKeyProvider()
    {
        return [
            [['a', 'b', 'd'], ['a', 'b', 'c'], 2],
            [['b' => 2, 'c' => 3], ['b' => 2, 'c' => 4], 'c'],
            [['b' => 2, 'c' => 3, 'd' => null], ['b' => 2, 'c' => 3, 'd' => 1], 'd'],
            [['b' => 2, 'c' => 3, 'd' => null], ['b' => 2, 'c' => 5], 'c'],
        ];
    }

    /**
     * @dataProvider nestedChangedKeyProvider
     */
    public function testFindsNestedChangedKeys($old, $new, $expectedKey)
    {
        $differ = new ArrayDiff();
        $diff = $differ->diff($old, $new);

        $this->assertArrayHasKey($expectedKey, $diff['changed']['a']['changed']);
    }

    public function nestedChangedKeyProvider()
    {
        return [
            [['a' => ['one' => 1, 'two' => 2, 'three' => 1, 'four' => []]], ['a' => ['one' => 1, 'two' => 1, 'three' => 1]], 'two'],
            [['a' => ['one' => 1, 'two' => 1, 'three' => 1, 'four' => []]], ['a' => ['one' => 2, 'two' => 1, 'three' => 1]], 'one'],
            [['a' => ['one' => 1, 'two' => 1, 'three' => 1, 'four' => []]], ['a' => ['one' => 1, 'two' => 1, 'three' => 3]], 'three'],
        ];
    }

    /**
     * @dataProvider changedValueProvider
     * @group changeValue
     */
    public function testChangedValueReturnsOldAndNewValues($old, $new, $expectedKey, $oldValue, $newValue)
    {
        $differ = new ArrayDiff();
        $diff = $differ->diff($old, $new);

        $this->assertArrayHasKey($expectedKey, $diff['changed']);
        $this->assertTrue(is_array($diff['changed'][$expectedKey]));
        $this->assertArrayHasKey('old', $diff['changed'][$expectedKey]);
        $this->assertArrayHasKey('new', $diff['changed'][$expectedKey]);
        $this->assertSame($oldValue, $diff['changed'][$expectedKey]['old']);
        $this->assertSame($newValue, $diff['changed'][$expectedKey]['new']);
    }

    public function changedValueProvider()
    {
        return [
            [['a', 'b', 'd'], ['a', 'b', 'c'], 2, 'd', 'c'],
            [['b' => 2, 'c' => 3], ['b' => 2, 'c' => 4], 'c', 3, 4],
            [['b' => 2, 'c' => 3, 'd' => null], ['b' => 2, 'c' => 3, 'd' => 1], 'd', null, 1],
            [['b' => 2, 'c' => 3, 'd' => null], ['b' => 2, 'c' => 5], 'c', 3, 5],
        ];
    }

    /**
     * @dataProvider nestedChangedValueProvider
     * @group nestedChangeValue
     */
    public function testNestedChangedValueReturnsOldAndNewValues($old, $new, $expectedKey, $oldValue, $newValue)
    {
        $differ = new ArrayDiff();
        $diff = $differ->diff($old, $new);

        $this->assertArrayHasKey($expectedKey, $diff['changed']['a']['changed']);
        $this->assertSame($oldValue, $diff['changed']['a']['changed'][$expectedKey]['old']);
        $this->assertSame($newValue, $diff['changed']['a']['changed'][$expectedKey]['new']);
    }

    public function nestedChangedValueProvider()
    {
        return [
            [['a' => ['one' => 1, 'two' => 2, 'three' => 1, 'four' => []]], ['a' => ['one' => 1, 'two' => 1, 'three' => 1]], 'two', 2, 1],
            [['a' => ['one' => 1, 'two' => 1, 'three' => 1, 'four' => []]], ['a' => ['one' => 2, 'two' => 1, 'three' => 1]], 'one', 1, 2],
            [['a' => ['one' => 1, 'two' => 1, 'three' => 1, 'four' => []]], ['a' => ['one' => 1, 'two' => 1, 'three' => 3]], 'three', 1, 3],
        ];
    }

    /**
     * @dataProvider nullValueProvider
     * @group nullValue
     */
    public function testNullValueComparison($old, $new, $expectedChangedKey)
    {
        $differ = new ArrayDiff();
        $diff = $differ->diff($old, $new);

        $this->assertArrayHasKey($expectedChangedKey, $diff['changed']);
    }

    public function nullValueProvider()
    {
        return [
            [['a', 'b', 'c', null], ['a', 'b', 'c', 'd'], 3],
            [['a', 'b', 'c', null], ['a', 'b', 'c', ['d']], 3],
            [['a', 'b', 'c', ['d']], ['a', 'b', 'c', null], 3],
        ];
    }
}
