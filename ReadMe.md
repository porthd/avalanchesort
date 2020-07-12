Remark 20200712
The current version is in develoment. The Quicksort and the avalanchesort should work poperly. 
- The work is in progress. 
- There are missing a lot of tests.
- There a missing a functional version for nodelists in PHP.
- There ist missing an advaced version of avalancheSort, which use wrongly sorted runs.

#statistik
##avalanche-sort versus quicksort 
200 elements, big randomized
(n = 200,
 n*lb(n) = 1528.77,
 n^2 = 40000

|     |.                 type |    QS |    AS | 
| --- | --------------------- | ----- | ----- | 
|  0. |           getDataList |     1 |     1 | 
|  1. |           setDataList |     1 |     1 | 
|  2. |           getDataItem |  3688 |  2850 | 
|  3. |         getFirstIdent |     1 |     2 | 
|  4. |          getNextIdent |   951 |  1473 | 
|  5. |        getMiddleIdent |     0 |     0 | 
|  6. |        getRandomIdent |     0 |     0 | 
|  7. |          getPrevIdent |  1229 |     0 | 
|  8. |          getLastIdent |     1 |     1 | 
|  9. |           isLastIdent |     0 |   302 | 
| 10. | cascadeDataListChange |     0 |    97 | 
| 11. |   cascadeData (moves) |     0 |   194 | 
| 12. |                  swap |   325 |     0 | 
| 13. |          swap (moves) |   975 |     0 | 
| 14. |       initNewListPart |     0 |    97 | 
| 15. |           addListPart |     0 |  1371 | 
| 16. | oddLowerEqualThanEven |  1844 |  1425 | 
 

# General
`avalanchesort` is a new sorting method, which should be better than mergesort or quicksort because it naturally make usage of presorted datats.
The algorithimn is independ to the form of storage and the form of the datas. 
The storage-handler only must define the methods, defined by the inferface `DataListAvalancheSortInterface.php`.  
In den documentation will you find a german description to the general idea of this algorithmn.

(* please excuse my germanised english ;-) *)

## installation 
Use sh-script on console via ddev-console, to start the composer.
I use phpstorm to execute the unittests with testsorting.


> 
