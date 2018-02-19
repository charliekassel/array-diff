<?php

namespace Differ;

/**
 * Class to find changes between two arrays
 *
 * Outputs an associative array with added, removed and changed keys
 *
 */
class ArrayDiff
{
    /**
     * @param array $old
     * @param array $new
     * @return array
     */
    public function diff(array $old, array $new): array
    {
        $added = $this->findAddedKeys($old, $new);
        $removed = $this->findRemovedKeys($old, $new);
        $changed = $this->findChangedKeys($old, $new);

        return compact('added', 'removed', 'changed');
    }

    /**
     * @param array $old
     * @param array $new
     * @return array
     */
    private function findAddedKeys(array $old, array $new): array
    {
        return array_filter($new, function ($key) use ($old) {
            return !array_key_exists($key, $old);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param array $old
     * @param array $new
     * @return array
     */
    private function findRemovedKeys(array $old, array $new): array
    {
        return array_filter($old, function ($key) use ($new) {
            return !array_key_exists($key, $new);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param array $old
     * @param array $new
     * @return array
     */
    private function findChangedKeys(array $old, array $new): array
    {
        $changed = array_filter($new, function ($newItem, $key) use ($old) {
            return array_key_exists($key, $old) && $old[$key] !== $newItem;
        }, ARRAY_FILTER_USE_BOTH);

        array_walk($changed, function (&$changedItem, $key) use ($old) {
            if (is_array($changedItem) && !is_null($old[$key])) {
                $changedItem = $this->diff($old[$key], $changedItem);
            } else {
                $changedItem = [
                    'old' => $old[$key],
                    'new' => $changedItem
                ];
            }
        });

        return $changed;
    }
}
