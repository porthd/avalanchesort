Remark 20200712
The current version is in develoment. 
The Quicksort and the avalanchesort should work poperly. 
- The work is in progress. 
- There are missing a lot of tests.

#statistik
##avalanche-sort versus quicksort and bubblesort 
200 elements in Associative array, big randomized

(n = 200, n*lb(n) = 1528.77, n^2 = 40000)

|     |.                 type |     BS |     QS |     AS |
| --- | --------------------- | ------ | ------ | ------ | 
|  0. |           getDataList |      1 |      1 |      1 |
|  1. |           setDataList |      1 |      1 |      1 |
|  2. |           getDataItem |  38912 |   3970 |   2848 |
|  3. |         getFirstIdent |      1 |      1 |      2 |
|  4. |          getNextIdent |  19456 |   1152 |   1478 |
|  5. |          getPrevIdent |      0 |   1165 |      0 |
|  6. |          getLastIdent |      1 |      1 |      1 |
|  7. |           isLastIdent |      0 |      0 |    302 |
|  8. | cascadeDataListChange |      0 |      0 |     96 |
|  9. |   cascadeData (moves) |      0 |      0 |   2136 |
| 10. |                  swap |   7458 |    304 |      0 |
| 11. |          swap (moves) |  22374 |    912 |      0 |
| 12. |       initNewListPart |      0 |      0 |     96 |
| 13. |           addListPart |      0 |      0 |   1375 |
| 14. | oddLowerEqualThanEven |  19456 |   1985 |   1424 |

# General
`avalanchesort` is a sorting method, which should be better than mergesort or quicksort because it naturally make usage of presorted datats.
The algorithimn is independ to the form of storage and the form of the datas. 
The storage-handler must define the methods of the inferface `DataListAvalancheSortInterface.php`.  
The folder documentation contain a german description to the general idea of this algorithmn.

(* please excuse my germanised english ;-) *)

## Requirements
php 7.3 (other versions may work)

## installation 
You can use your PHP-envoirement. I prefer docker and ddev.
The config for ddev (https://ddev.readthedocs.io/en/stable/) is part of the code. 
Use composer, to install phpunit. 

I use phpstorm to execute the unittests with testsorting. You can although use your console/bash.


> 
