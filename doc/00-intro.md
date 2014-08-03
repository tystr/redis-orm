Intro
=====

RedisOrm is a simple library to facilitate storing and finding objects in redis
in an efficient manner.

Each object is stored with it's properties as a hash under a key named by the
object's `Id`. To facilitate searching for an object using it's property values
as criteria, indexes can be created. An "index" is a key with the property name
and value as the key name, and a set of `Id`s of objects whose property values
match that of the key. Filtering is done by intersecting these index sets to find
objects that match all the given criteria. This is an extremely efficient way to
filter large amounts of data.

This library ensures that the appropriate keys are set/unset as neededw while
providing a simple interface through which to access your objects.

Next: [Installation](01-installation.md)
