Usage
=====

First, you will need to define some mapping metadata on your models:

```PHP

<?php

use Tystr\RedisOrm\Annotations\Id;
use Tystr\RedisOrm\Annotations\Index;
use Tystr\RedisOrm\Annotations\Date;

class Car
{

    /**
     * @var int
     * @Id
     */
    protected $id;
    
    /**
     * @var string
     * @Index
     */
    protected $name;
    
    /**
     * @var \DateTime
     * @Date
     */
    protected $createdAt;

    /**
     * @param int       $id
     * @param string    $name
     * @param \DateTime $createdAt
     */
    public function __construct($id, $name, \DateTime $createdAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = $createdAt;
    }
    // Getters and Setters
}

```

Now, the repository class will know which indexes to create and what to use as the values.

Instantiating the `PredisRepository` class and saving your object is simple:
```PHP
<?php

require 'vendor/autoload.php';

$redis = new Predis\Client();
$keyNamingStrategy = new Tystr\RedisOrm\KeyNamingStrategy\ColonDelimitedKeyNamingStrategy();
$repository = new PredisRepository($client, $keyNamingStrategy));

$car = new Car(123, 'Tesla', new \DateTime());

$repository->save($car);
```

TODO: Finding objects

Next: [Annotations](03-annotations.md)