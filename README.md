# CsvStreamedResponse

CSV streamed response that saves memory usage with symfony and doctrine.

## Usage

### example 1: Specify columns with DQL

```php
class DefaultController extends Controller
{
    public function csvAction(UserRepository $repository)
    {
        return CsvStreamedResponse::builder()
            ->setRowsFromDoctrineQueryBuilder(
                $repository->createQueryBuilder('u')->select('u.id, u.name')
            )
            ->build()
        ;
    }
}
```

### example 2: Specify columns with entity methods

```php
class DefaultController extends Controller
{
    public function csvAction(UserRepository $repository)
    {
        return CsvStreamedResponse::builder()
            ->setRowsFromDoctrineQueryBuilder(
                $repository->createQueryBuilder('u'),
                function ($user) {
                    return [
                        $user->getId(),
                        $user->getName(),
                    ];
                }
            )
            ->setCsvColumnHeaders([
                'user_id',
                'user_name',
            ])
            ->build()
        ;
    }
}
```

### example 3: Fetch join a to-many collection in QueryBuilder

Suppress error for `Iterate with fetch join in class ...`.

```php
class DefaultController extends Controller
{
    public function csvAction(UserRepository $repository)
    {
        $qb = $repository->createQueryBuilder('u')
            ->leftJoin('u.emails, 'em') // Join to-many collection
        ;

        return CsvStreamedResponse::builder()
            ->setRowsFromDoctrineQueryBuilder(
                $qb,
                function ($user) {
                    return [
                        $user->getId(),
                        $user->getName(),
                        implode(',', $user->getEmails()),
                    ];
                },
                $fetchJoinCollection = true
            )
            ->setCsvColumnHeaders([
                'user_id',
                'user_name',
                'emails,
            ])
            ->build()
        ;
    }
}
```
