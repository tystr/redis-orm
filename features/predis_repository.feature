Feature: Repository persistence
  Objects should be stored in redis with the appropriate "index" keys

  Scenario: Saving an object with "indexed" properties
    Given the following Car:
      | id | make  | model   | engine_type | color |
      | 1  | Tesla | Model S | V8          | red   |
    Then there should be 6 keys in the database
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