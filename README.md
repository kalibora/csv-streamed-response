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
