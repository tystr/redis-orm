Feature: Repository persistence
  Objects should be stored in redis with the appropriate "index" keys

  Scenario: Saving an object with "indexed" properties
    Given the following Car:
      | id | make  | model   | engine_type | color |
      | 1  | Tesla | Model S | V8          | red   |
    Then there should be 7 keys in the database
    And the following keys should exist:
      | name           | value |
      | make:Tesla     | 1     |
      | model:Model S  | 1     |
      | engine_type:V8 | 1     |
      | color:red      | 1     |

  Scenario: Finding an object by it's id
    Given the following Car:
      | id | make  | model   | engine_type | color |
      | 1  | Tesla | Model S | V8          | red   |
    And the car with id "1" has the property "attributes" with the following values:
      | is_favorite | yes |
      | is_slow     | no  |
      | is_awd      | yes |
    And the car with id "1" has the property "owners" with the following values:
      | 0 | one |
      | 1 | two |
    When I find a Car by id 1
    Then there should be 1 car
    And the car with the id 1 should have the following properties:
      | id | make  | model   | engine_type | color |
      | 1  | Tesla | Model S | V8          | red   |
    And the car with the id 1 should have property "attributes" with the following values:
      | is_favorite | yes |
      | is_slow     | no  |
      | is_awd      | yes |

    @findby
  Scenario: Finding an object by a property value
    Given the following Car:
      | id | make  | model   | engine_type | color |
      | 1  | BMW   | M5      | V10         | red   |
      | 2  | Tesla | Model S | what?       | blue |
    When I find cars where the property "color" is "red"
    Then there should be 1 car

  Scenario: Saving an object with null property values removes the id from index field for that property
    Given the following Car:
      | id | make    | model   | engine_type | color  |
      | 1  | Tesla   | Model S | V8          | red    |
      | 2  | Porsche | 911     | V8          | yellow |
    Then there should be 2 items in the "manufacture_date" key
    When I set the manufacture date to null
    Then there should be 1 items in the "manufacture_date" key
    And When I set the color for the car "1" to "null"
    Then there should be 0 items in the "color:red" key

  Scenario: Saving an object with a different property value removes the id from the index of the original value;
    Given the following Car:
      | id | make    | model   | engine_type | color  |
      | 1  | Tesla   | Model S | V8          | red    |
    And When I set the color for the car "1" to "blue"
    Then there should be 0 items in the "color:red" key
    And there should be 1 items in the "color:blue" key

  @boolean
  Scenario: Saving an object with a boolean property
    Given the following Car:
      | id | make    | model   | engine_type | active |
      | 1  | Tesla   | Model S | V8          | 1      |
    Then there should be 1 items in the "active:1" key
    And there should be 0 items in the "active:0" key
    And When I set the active for the car "1" to "false"
    Then there should be 0 items in the "active:1" key
    And there should be 1 items in the "active:0" key
    And the car with the id "1" should have property "active" with the value "false":

  @filter
  Scenario: Finding and filtering data
    Users exist in the database
    Lists contain criteria that determines which users belong to the list
    Filtering the users using a list should return only users who's data match the lists criteria

    Given the following users:
      | email           | dob        | signup     | last_open  | last_click | email_opt | subscribed |
      | test@test.com   | 1980-01-01 | 2014-06-25 | 2014-07-01 | 2014-07-01 | 1         | 1          |
      | test1@test.com  | 1980-01-01 | 2014-06-25 | 2014-07-01 | 2014-07-01 | 1         | 1          |
      | test2@test.com  | 1980-01-01 | 2014-06-25 | 2014-07-01 | 2014-07-01 | 1         | 1          |
      | test3@test.com  | 1980-01-01 | 2014-06-25 | 2014-07-01 | 2014-07-01 | 1         | 1          |
      | test4@test.com  | 1980-01-01 | 2014-06-25 | 2014-07-01 | 2014-07-01 | 1         | 1          |

      | test5@test.com  | 1980-01-01 | 2014-06-25 | 2014-06-01 | 2014-07-01 | 1         | 1          |
      | test6@test.com  | 1980-01-01 | 2014-06-25 | 2014-06-01 | 2014-07-01 | 1         | 1          |
      | test7@test.com  | 1980-01-01 | 2014-06-25 | 2014-06-01 | 2014-07-01 | 1         | 0          |
      | test8@test.com  | 1980-01-01 | 2014-06-25 | 2014-06-01 | 2014-07-01 | 1         | 1          |

      | test9@test.com  | 1980-01-01 | 2014-06-25 | 2013-06-01 | 2014-07-01 | 0         | 1          |
      | test10@test.com | 1980-01-01 | 2014-06-25 | 2013-06-01 | 2014-07-01 | 1         | 1          |
      | test11@test.com | 1980-01-01 | 2014-06-25 | 2013-06-01 | 2014-07-01 | 1         | 1          |

    Given the list "test" has the following criteria:
      | name        | key          | value      |
      | equalTo     | subscribed   | 1          |
      | greaterThan | last_open    | 2014-01-01 |
      | lessThan    | last_open    | 2014-06-30 |
    Then the list "test" should have 3 users

    Given the list "test2" has the following criteria:
      | name        | key          | value      |
      | lessThan    | last_open    | 2014-06-30 |
    Then the list "test2" should have 7 users

    Given the list "test3" has the following criteria:
      | name        | key          | value      |
      | greaterThan | last_open    | 2014-06-30 |
    Then the list "test3" should have 5 users

    Given the list "test4" has the following criteria:
      | name        | key          | value      |
      | equalTo     | subscribed   | 1          |
    Then the list "test4" should have 11 users

    Given the list "test5" has the following criteria:
      | name        | key          | value      |
      | equalTo     | subscribed   | 1          |
      | equalTo     | email_opt    | 1          |
    Then the list "test5" should have 10 users


  @filter-or
  Scenario: simple or
    Given the following users:
      | email           | dob        | signup     | last_open  | last_click | email_opt | subscribed |
      | test1@test.com  | 1980-01-01 | 2014-06-25 | 2014-07-01 | 2014-07-01 | 1         | 1          |
      | test2@test.com  | 1980-01-02 | 2014-06-26 | 2014-07-02 | 2014-07-02 | 0         | 1          |
      | test3@test.com  | 1980-01-03 | 2014-06-27 | 2014-07-03 | 2014-07-03 | 1         | 0          |

    And the list "test6" has the following criteria:
      |   name     | key       |  value         |
      |   equalTo  | email_opt | 1              |
      |   equalTo  | email_opt | 0              |
      |   orGroup  |           | 1,2            |
    Then the list "test6" should have 3 users
    And the list "test6" should have the ids "test1@test.com,test2@test.com,test3@test.com"


  @filter-and
  Scenario: simple and
    Given the following users:
      | email           | dob        | signup     | last_open  | last_click | email_opt | subscribed |
      | test1@test.com  | 1980-01-01 | 2014-06-25 | 2014-07-01 | 2014-07-01 | 1         | 1          |
      | test2@test.com  | 1980-01-02 | 2014-06-26 | 2014-07-02 | 2014-07-02 | 0         | 1          |
      | test3@test.com  | 1980-01-03 | 2014-06-27 | 2014-07-03 | 2014-07-03 | 1         | 0          |

    And the list "test7" has the following criteria:
      |   name         | key        |  value   |
      |   equalTo      | email_opt  | 1        |
      |   equalTo      | subscribed | 0        |
      |   andGroup     |            | 1,2      |
    Then the list "test7" should have 1 users
    And the list "test7" should have the ids "test3@test.com"


  @filter-nested-composition
  Scenario: nested composition
    Given the following users:
      | email           | dob        | signup     | last_open  | last_click | email_opt | subscribed |
      | test1@test.com  | 1980-01-01 | 2014-06-25 | 2014-07-01 | 2014-07-01 | 1         | 1          |
      | test2@test.com  | 1980-01-02 | 2014-06-26 | 2014-07-02 | 2014-07-02 | 0         | 1          |
      | test3@test.com  | 1980-01-03 | 2014-06-27 | 2014-07-03 | 2014-07-03 | 1         | 0          |

    And the list "test8" has the following criteria:
      |   name        | key        |  value     |
      |   equalTo     | email_opt  | 1          |
      |   equalTo     | email_opt  | 0          |
      |   greaterThan | last_open  | 2014-07-01 |
      |   orGroup     |            | 1,2        |
      |   andGroup    |            | 3,4        |


    Then the list "test8" should have 2 users
    And the list "test8" should have the ids "test2@test.com, test3@test.com"