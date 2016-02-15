# repository-package

This package was developed to extract out the repository layer from the tenant application but to also use the same repository layer throughout other projects too.

This package is inspired from and based on:

* https://github.com/andersao/l5-repository
* https://github.com/Bosnadev/Repositories

To package gets installed automatially when you pull in the tenant-package, the service provider is bootstrapped automatically too.

But to manually install it in a project, add this to your `composer.json` file:

```
"archiveonline/repository-package": "dev-master"
```

Since this is a private package like the tenant-package, it must be pulled in by listing a vcs in the composer file like so:

```
"repositories": [
    {
        "type": "vcs",
        "url": "https://rohan0793@bitbucket.org/archiveonline/repository-package.git"
    }
],
```

Note: Don't forget to change the username in that URL.

And run `composer update`.

Add the service provider in the `app.php` file in the provider's array:

```
Uppdragshuset\AO\Repository\RepositoryServiceProvider::class,
```

It comes with a basic `make:repository` command which is helpful in bootstrapping basic repositories, presenters and transformers for the project the package is being used for.

To make use the command, refer to this example:

```
php artisan make:repository Post
```

This will make a Post repository interface and an eloquent implementation of it along with a transformer and a presenter too. This will all be namespaced and placed in the `App` namespace but the models used in the stubs are using the `Uppdragshuset\AO\Tenant\Models` namespace so beware when trying to use models in another namespace.

An abstract base class for the eloquent implementation contains all basic methods like `all()`, `paginate()`, `find()`, `findBy()`, `update()`, `delete()` and so on. Kindly go through the code once to have a better understanding of how these methods work and which all are available and how they handle the authorization internally using laravel 5 policies.

Once the repositories, transformers and presenters are generated, they still need work. The namespaces for the models should be corrected if required. The interface needs to be bound with the implemetaion like so:

```
 $this->app->bind(
    App\Repositories\PostRepository::class,
    App\Repositories\EloquentPostRepository::class
);
```

To use it in a controller, import the classes like so:

```
use App\Repositories\PostRepository as Comment;
use App\Repositories\Presenters\PostPresenter;
```

Then instantiate them using the constructor like so:

```
protected $post;
protected $presenter;

public function __construct(Post post, PostPresenter $presenter)
{
    $this->post = post;
    $this->presenter = $presenter;
}
```

Methods on the repository can then be easily called like so:

```
$this->post->find(1);
$this->post->delete(1);
$this->post->with(['comments'])->paginate(10);
```

Note: As mentioned earlier, to understand better what all is available in the repository package, at least go through the `BaseRepository` code.