Access you data via dot-notation, no matter if it's an array, a stdClass or a custom object!

## Install

```sh
$ composer require simones/dotnot
```

## Usage

Construct a `DotNot` instance with your data and access it with the `get` method: that's all! Your data can be composed of any combination of arrays, stdClass instances and custom classes' instances: they all behave the same.
You can also use the `dotnot` helper function, which accepts a mandatory `$root` argument and an optional `$path` to resolve.

Here are some examples (for more, head to the spec):

```php
// stdClass + array
dotnot((object) [
    'author' => [
        'name' => 'Simone Salerno'
    ]
])->get('author.name')

// Custom class, with getter method
class Author {
    public function getAuthorName() {
        return 'Simone Salerno';
    }
}

// this looks for a method named "get" . ucfirst($getter)
dotnot(new Author)->get('authorName')

// when it can't resolve the path, it throws an exception
// so you should catch it or test for existence
dotnot(new Author)->get('foo') // throws DotNotException
//or
$dotnot = dotnot(new Author);

if ($dotnot->has('foo')) {
    // do some work...
}

// it also works with numeric indexes
dotnot([
    'people' => [
        0 => (object) [
            'author' => new Author
        ]
    ]
])->get('people.0.author.authorName')
```