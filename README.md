# CsvStreamedResponse

CSV streamed response that saves memory usage with symfony and doctrine.

## Usage

### example 1: Specify columns with DQL

```php
class DefaultController extends Controller
{
	public function csvAction(UserRepository $repository)
    {
        return CsvStreamedResponse::createFromDoctrineQueryBuilder(
            $userRepository->createQueryBuilder('u')->select('u.id, u.name')
        );
    }
}
```

### example 2: Specify columns with entity methods

```php
class DefaultController extends Controller
{
	public function csvAction(UserRepository $repository)
    {
        return CsvStreamedResponse::createFromDoctrineQueryBuilder(
            $userRepository->createQueryBuilder('u'),
            [
                'user_id',
                'user_name',
            ],
            function ($user) {
                return [
                    $user->getId(),
                    $user->getName(),
                ];
            }
        );
    }
}
```
