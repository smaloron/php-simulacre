#Simulacre
Simulacre is a php tool to generate fake data for testing or demo databases.

##Running
Open a terminal window in the app/src folder and type :

```shell
    php simulacre.php nameOfYourProjetcFile
```
##Project Files
The project file contains the parameters of your data generation project. 
They are stored in the **_projects_** folder of the application.
Those files contains nested associative array to represents the different levels of configuration.

###Basic configuration
At the first level of the config array you can have the following keys :
                
- **tables** :      An array representing the tables of you project
- **dataSources** : An associative array containing PDO instances if you want to use a specific database connection either to retrieve information or insert data.

###Table configuration
Each entry under the tables key contains an array referencing a table configuration.

- **tableName** : required : A string representing the table name, this name will be used as the name of the output file or the name of the database table if the output is set to insert directly in a database.
- **NumberOfRecords** : default value 10 : An integer representing the number of records to be generated for this table.
- **outputParams** : An associative array that sets how the table will be outputted. It will be discussed further in the output chapter. If the outputParams are missing, the default value map to a CSV output.
- **fields** : required : An associative array storing the fields of the table. The keys will be used as fields names for the output.


So a project file skeleton would looks like that : 


```php
$config = [
    'tables' => [
        [
            'tableName'         => 'contacts',
            'numberOfRecords'   => 1000,
            'fields'            => [
                                        'name'          => []
                                        'first_name'    => []
                                    ]
        ],
        [
            'tableName          => 'orders'
            'numberOfRecords'   => 100,
            'fields'            => [
                                        'order_date'    => []
                                        'order_amount'  => []
                                    ]
        ]
    ]
];
```

###Fields and Generators
The first and required key of a field array is the generator. It is a link to the class that will generate the data.
All fields share those keys, there are also some specific keys that depends on the chosen generator.

- **generator** : required : A string defining the generator to be used.
- **percentOfNull** : default 0 : An integer defining the probability of getting a null value.
- **includedInOutput** : default true : A boolean defining whether the field should be included in the output. Some random data are just used to support other generators and therefore should not appears in the output.  
- **formatters** : An array of formatters that will be applied to the generated value, this will be discussed later.

####Constant generator
generator key : constant

- **constantValue** : required : The constant value that will be returned by this generator.

####Integer generator
generator key : integer

- **minValue**      : default 0 :    The lower bound of the range of integer values to be randomly generated.
- **maxValue**      : default 10 :   The upper bound of the range of integer values to be randomly generated.
- **targetMean**    : The target mean of the whole population of generated values.
- **numberOfRoll**  : default 1 : The number of rolls used to generate the value. A greater number of rolls will generate a gaussian distribution.
      
####Real generator
generator key : real
This generator inherits from the integer generator so all the integer keys can be used.

- **numberOfDecimals** : default 2 : the number of decimals of the generated real value.

####Date generator
generator key : date

- **dateFormat**    : default Y-m-d : The format of the startDate and endDate parameters.
- **startDate**     : required : The lower bound of the range of date time values to be randomly generated. 
- **endDate**       : required : The upper bound of the range of date time values to be randomly generated. 
- **outputFormat**  : default Y-m-d H:i:s : The format of the outputted value. 

the format to be used for the date can be found here http://php.net/manual/en/datetime.createfromformat.php

####Weighted range generator
generator key : weighted
This generator output values according to a weighted range associative array.
  
- **weightedRange** : required : An associative array where the keys represents the actual values and the values represents the weight.

###From text file generator
generator key : textFile
This generator pick a random line from a delimited text file and returns the value at a specified column number.

- **basePath**              : The path to the text file, by default it will be the data folder sitting in the src folder of the Simulacre app.
- **fileName**              : required : The name of the file.
- **recordSeparator**       : default \n : The lines separator.
- **columnSeparator**       : default \t : The column separator.
- **firstDataLineNumber**   : default 0 : The first line that contains data. If the text file has a one line header, this parameter should be set to 1.
- **returnedColumnNumber**  : default 0 : The column number that will be used to get the value, by default this is set to 0 so the first column of the file.

####From database generator
generator key : databaseQuery

This generator will execute an sql query and returns a random line from this query.

- **dataSource**                : required : A string which is a key defined in the dataSources parameter array of the top level configuration. This array stores PDO instances.
- **sqlString**                 : required : A string representing the select query to retrieve the data to be randomly picked.
- **fieldName**                 : A string representing a field name present in the sql query. This field name will be used to get the generated value. If the field name parameter is missing, the first field of the query will be used.
- **queryParameters**           : An associative array representing the PDO parameters to use for executing the query. Those parameters can be constants or links to other generator values (this could be a case where a field should net be included in the ouput since its value would only serve as a random parameter to a query generator).
- **newQueryForEachIteration**  : default false : By default the query is executed only once and its results stored for all the iteration. If you want to reexecute the query at each iteration, this parameter should be set to true. Notice that if you define a query parameter that links to another generator, the newQueryForEachIteration will be automatically set to true since the query parameters will change at each iteration.

####Foreign key generator
generator key : foreignKey

This generator inherits from the databaseQuery generator so all the databaseQuery keys are allowed.
The foreignKey generator is a kind of specialized database query generator. It permits to create a random number of records for each key of a query.


- **numberOfValuesByRecords**  : default 1 : either an integer or a string. If it's an integer it will represents a constant number of records to be created for each record in the query. If it's a string, it should use this format 'mintomax', for example 0to5 means that for each foreign key the program will generate a random number of records form 0 to 5.

Important note, since this generator will generate un random number of records for each foreign key, the numberOfRecords parameter of the table no longer apply. The generation job will stop when the last record of the foreign key query has been processed.

####look up generator
generator key : lookUp

This generator look up a value from another field's generator.

- **lookUpFieldName**   : required : The name of the field that contains the look up value.
- **lookUpKey**         : the key either numeric or alphabetic used to retrieve a value when the field returns an array.

example

```php
    //This generator lookup the field first_name and get the value of the column sex 
    //this assume that first_name comes from a database or text file generator and that a column named sex exists in the dataset
    'sex'          => [
                'generator'       => 'lookUp',
                'lookUpFieldName' => 'first_name',
                'lookUpKey'       => 'sex'
            ]
```
 
###Formatters
Formatters are anonymous functions used to change the generated value of a field.

example

```php
    // This formatter convert the generated value to upper case
    'person_name'          => [
                'generator'     => 'name',
                'formatters'    => [
                    function ($value){
                        return strtoupper($value);
                    }
                ]
    ]
```

```php
    //This generator lookup the field first_name and get the value of the column sex 
    //this assume that first_name comes from a database or text file generator and that a column named sex exists in the dataset
    //Since the sex column can have three values (female, male or both), we define a formatter to choose between male or female when we get a both value
    'sex'          => [
                'generator'       => 'lookUp',
                'lookUpFieldName' => 'first_name',
                'lookUpKey'       => 'sex',
                'formatters'    => [
                                    function ($value){
                                        if($value == 'male' || $value == 'female'){
                                            return $value[0];
                                        } else {
                                            if(mt_rand(1,100) < 50){
                                                return 'm';
                                            }else {
                                                return 'f';
                                            }
                                        }
                                    }
                                ]
            ]
```


###Stopping rules
Stopping rules are anonymous functions that returns boolean values.
 
 ```php
 'stoppingRule' => function(\Simulacre\SimulacreTable $table){
             $field =  $table->getFieldByName('age');
                 return $field->getTotal() > 500;
         }
 ```
