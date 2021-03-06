<?php
declare(strict_types=1);

namespace KGzocha\Searcher;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 */
abstract class AbstractCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var array
     */
    private $items;

    /**
     * @param \Traversable|array $items
     */
    public function __construct($items = [])
    {
        $this->items = [];
        $this->isTraversable($items);
        $this->checkItems($items);

        $this->items = $items;
    }

    /**
     * @param object $item
     *
     * @return bool
     */
    abstract protected function isItemValid($item): bool;

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Iterator
    {
        foreach ($this->items as $key => $item) {
            yield $key => $item;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @param mixed $item
     *
     * @return self
     */
    protected function addItem($item): AbstractCollection
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $item
     *
     * @return self
     */
    protected function addNamedItem(string $name, $item): AbstractCollection
    {
        $this->items[$name] = $item;

        return $this;
    }

    /**
     * @return array
     */
    protected function getItems()
    {
        return $this->items;
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    protected function getNamedItem(string $name)
    {
        if (array_key_exists($name, $this->items)) {
            return $this->items[$name];
        }

        return null;
    }

    /**
     * @param mixed $items
     *
     * @throws \InvalidArgumentException
     */
    private function isTraversable($items)
    {
        if (is_array($items)) {
            return;
        }

        if ($items instanceof \Traversable) {
            return;
        }

        throw new \InvalidArgumentException(sprintf(
            'Argument passed to collection %s needs to be an array or traversable object',
            get_class($this)
        ));
    }

    /**
     * @param array|\Traversable $items
     *
     * @throws \InvalidArgumentException
     */
    private function checkItems($items)
    {
        foreach ($items as $item) {
            if ($this->isItemValid($item)) {
                continue;
            }

            throw new \InvalidArgumentException(sprintf(
                'At least one item in collection "%s" is invalid.',
                get_class($this)
            ));
        }
    }
}
