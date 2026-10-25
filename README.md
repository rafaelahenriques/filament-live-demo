# Filament Live: a Filament PHP demo

A small live poll built with **Filament PHP v5** on **Laravel 13**. The audience scans a QR code with their phones, logs in and answers one question: *"Would you use Filament for your next admin panel?"* The projector shows the answers arriving live in a table, in counters and in a chart.

The whole demo is about 110 lines of PHP, and you don't write any JavaScript yourself.

- [1. Files changed](#1-files-changed)
- [2. Build it step by step](#2-build-it-step-by-step)
- [3. The code, explained](#3-the-code-explained)
- [4. Run the demo](#4-run-the-demo)

---

## 1. Files changed

Everything else in this folder is the standard output of `composer create-project laravel/laravel` and `filament:install`.

| File | Created by | What I changed |
|---|---|---|
| `database/migrations/…_create_votes_table.php` | `make:model Vote -m` | Added 3 columns: `name`, `verdict`, `reason` |
| `app/Models/Vote.php` | `make:model Vote -m` | Listed the fields that may be saved, plus the 3 answers |
| `app/Filament/Resources/Votes/VoteResource.php` | `make:filament-resource` | Wrote the form and the table |
| `app/Filament/Resources/Votes/Pages/ManageVotes.php` | `make:filament-resource` | Removed the default "New vote" button (it moved into the table) |
| `app/Filament/Widgets/VoteStats.php` | `make:filament-widget` | QR code and the live Yes / Maybe / No counters |
| `app/Filament/Widgets/VoteChart.php` | `make:filament-widget` | Live bar chart of the answers |
| `app/Providers/Filament/AdminPanelProvider.php` | `filament:install` | Name, dark mode and purple colour; removed the 2 default dashboard cards |
| `start.sh` | written by hand | Starts the server and prints the link to open |

---

## 2. Build it step by step

These steps are for macOS with [Homebrew](https://brew.sh). Each command is followed by what it does.

### Install the tools

**1. Install PHP**
```bash
brew install php
```
PHP is the programming language Laravel and Filament are written in.

**2. Install Composer**
```bash
brew install composer
```
Composer is PHP's package manager, like `npm` for JavaScript.

### Create the project

**3. Create a new Laravel app**
```bash
composer create-project laravel/laravel filament-live-demo
```
Downloads a fresh Laravel app into the folder `filament-live-demo`. Laravel uses SQLite by default, so the database file is created for you.

**4. Go into the folder**
```bash
cd filament-live-demo
```

**5. Install Filament**
```bash
composer require filament/filament:"^5.0" -W
```
Adds Filament version 5 to the project. `-W` lets Composer update the packages Filament depends on.

**6. Create the admin panel**
```bash
php artisan filament:install --panels --no-interaction
```
`php artisan` is Laravel's command-line tool. This creates an admin panel at `/admin` with a login page and a dashboard. `--no-interaction` means "don't ask me questions, use the defaults" (the panel is called `admin`).

**7. Create the login account**
```bash
php artisan make:filament-user --name=Demo --email=demo@filament.test --password=filament
```
Creates the account everyone logs in with: you on the projector and the audience on their phones.

### Add the data

**8. Create the Vote model and its database table**
```bash
php artisan make:model Vote -m
```
Creates `app/Models/Vote.php` (the model) and, because of `-m`, a migration file in `database/migrations/`.

Open the new `database/migrations/…_create_votes_table.php` and add the 3 lines in the middle:
```php
Schema::create('votes', function (Blueprint $table) {
    $table->id();
    $table->string('name');              // add
    $table->string('verdict');           // add
    $table->text('reason')->nullable();  // add
    $table->timestamps();
});
```

Delete everything in `app/Models/Vote.php` and paste the full file from [section 3](#votephp).

**9. Create the table in the database**
```bash
php artisan migrate
```
Runs the migration, so the `votes` table now exists.

### Build the screens

**10. Generate the Votes page**
```bash
php artisan make:filament-resource Vote --simple --no-interaction
```
Creates a "resource": the screens to list, create and edit votes. `--simple` puts everything on one page and opens forms in pop-up windows.

Delete everything in these two files and paste the full files from section 3, starting with the `<?php` line:
- `app/Filament/Resources/Votes/VoteResource.php` → [VoteResource.php](#voteresourcephp)
- `app/Filament/Resources/Votes/Pages/ManageVotes.php` → [ManageVotes.php](#managevotesphp)

**11. Generate the counters widget**
```bash
php artisan make:filament-widget VoteStats --stats-overview --panel=admin --no-interaction
```
Creates `app/Filament/Widgets/VoteStats.php`. Delete everything in it and paste the full file from [section 3](#votestatsphp).

**12. Generate the chart widget**
```bash
php artisan make:filament-widget VoteChart --chart --panel=admin --no-interaction
```
Creates `app/Filament/Widgets/VoteChart.php`. Delete everything in it and paste the full file from [section 3](#votechartphp).

**13. Style the panel**

In `app/Providers/Filament/AdminPanelProvider.php`:
- add `->brandName('Filament Live')` and `->darkMode(isForced: true)` after `->login()`,
- change `Color::Amber` to `Color::hex('#8f7cf7')`,
- delete the `->widgets([...])` block and its two `use` lines (`AccountWidget`, `FilamentInfoWidget`), so the dashboard only shows your widgets.

**14. Add the start script**

Create a file called `start.sh` in the project folder, paste the full file from [section 3](#startsh), then make it runnable:
```bash
chmod +x start.sh
```

**15. Start the demo**
```bash
./start.sh
```
See [section 4](#4-run-the-demo).

---

## 3. The code, explained

### PHP in one minute (for JavaScript developers)

| PHP | Means | JavaScript / TypeScript equivalent |
|---|---|---|
| `<?php` | must be the first line of every PHP file; without it the file is shown as plain text | (none) |
| `namespace App\Models;` | the "address" of the classes in this file; it matches the folder (`app/Models`) | the file path you import from |
| `use App\Models\Vote;` | import a class (`\` separates the parts of the name, like folders) | `import { Vote } from '@/models/Vote'` |
| `$name` | a variable (always starts with `$`) | `name` |
| `->` | call a method or read a property | `.` |
| `::` | use something that belongs to the class itself (static) | `ClassName.method()` |
| `Vote::class` | the class's full name as text (`'App\Models\Vote'`) | (none) |
| `fn ($x) => $x * 2` | short arrow function | `(x) => x * 2` |
| `['a', 'b']` | a list | `['a', 'b']` |
| `['yes' => 'Yes']` | a list with keys (PHP uses `[]` for both) | `{ yes: 'Yes' }` |
| `"<img src='{$qr}'>"` | text with a variable inside (double quotes only) | `` `<img src='${qr}'>` `` |
| `?string`, `int`, `bool` | types; `?string` means "text or null" | `string \| null`, `number`, `boolean` |
| `darkMode(isForced: true)` | named argument | `darkMode({ isForced: true })` |
| `#[Fillable([...])]` | an *attribute*: a label attached to a class | a decorator, `@Fillable([...])` |
| `class A extends B` | A inherits everything from B | same |

**The Filament pattern.** In every Filament file you extend a ready-made Filament class and then either set a property (like `$pollingInterval = '2s'`) or fill in a method (like `form()` or `getStats()`). Filament does the rest.

**How the page updates without your own JavaScript.** Filament is built on **Livewire**. Your PHP runs on the server and produces HTML. Filament's built-in JavaScript sends each click (and each timed refresh) to the server, runs your PHP again, and swaps in only the parts of the page that changed. So `->live()`, `->poll('2s')` and `$pollingInterval` all mean "ask the server for a fresh version".

---

### The migration

A *migration* is a PHP file that creates or changes database tables; `php artisan migrate` runs the ones that haven't run yet (like Prisma or Knex migrations). This one creates the `votes` table:

```php
Schema::create('votes', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('verdict');
    $table->text('reason')->nullable();
    $table->timestamps();
});
```
- `Schema::create('votes', function (Blueprint $table) { ... })`: create a table called `votes`; inside the function, `$table` is used to add the columns.
- `$table->id()`: a numeric ID that counts up automatically.
- `string('name')` and `string('verdict')`: short text columns.
- `text('reason')->nullable()`: a longer text column that may be empty (only "No" voters fill it in).
- `timestamps()`: adds `created_at` and `updated_at`, filled in automatically.

---

### Vote.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'verdict', 'reason'])]
class Vote extends Model
{
    public const VERDICTS = ['yes' => 'Yes', 'maybe' => 'Maybe', 'no' => 'No'];
}
```

- `class Vote extends Model`: a *model* is the PHP class for a database table; one `Vote` object is one row. Laravel guesses the table name from the class name: `Vote` → `votes` (lower case, plural). That's why the migration creates a table called `'votes'`.
- `#[Fillable([...])]`: the columns Laravel may copy in one go from user input when a form is saved. Anything else in that input is ignored, so a user can't sneak in extra fields.
- `VERDICTS`: the 3 possible answers. The key (`'yes'`) is saved in the database, the value (`'Yes'`) is shown on screen. The form, table, counters and chart all read this one list.

---

### VoteResource.php

```php
<?php

namespace App\Filament\Resources\Votes;

use App\Filament\Resources\Votes\Pages\ManageVotes;
use App\Models\Vote;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VoteResource extends Resource
{
    protected static ?string $model = Vote::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Your name')->required(),
            ToggleButtons::make('verdict')->label('Would you use Filament for your next admin panel?')
                ->options(Vote::VERDICTS)->inline()->live()->required(),
            Textarea::make('reason')->label('Why not?')
                ->visible(fn (Get $get): bool => $get('verdict') === 'no')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                SelectColumn::make('verdict')->options(Vote::VERDICTS)->selectablePlaceholder(false),
                TextColumn::make('reason')->wrap(),
                TextColumn::make('created_at')->since(),
            ])
            ->headerActions([CreateAction::make()])
            ->defaultSort('created_at', 'desc')
            ->poll('2s');
    }

    public static function getPages(): array
    {
        return ['index' => ManageVotes::route('/')];
    }
}
```

A *resource* connects Filament to a model. `$model = Vote::class` tells it which model to manage. It appears as **Votes** in the menu.

**The form** (`form()`) is a list of fields. Each field is created with `::make('column')` and then configured with chained methods.
- `TextInput::make('name')`: a text box that saves into the `name` column. `->label(...)` is the text above it, `->required()` means it can't be empty.
- `ToggleButtons::make('verdict')`: a row of buttons (Yes, Maybe, No) taken from `Vote::VERDICTS`. `->inline()` puts them next to each other.
- `->live()`: every time you click a button, the form is sent to the server and redrawn. That's what lets the next field react.
- `Textarea::make('reason')`: a bigger text box.
- `->visible(fn (Get $get): bool => $get('verdict') === 'no')`: a small function that decides if the box is shown. `$get('verdict')` reads the current answer, and the box only appears when it is `'no'`. While it is hidden, `->required()` doesn't apply and its text isn't saved.

**The table** (`table()`):
- `TextColumn::make('name')->searchable()`: shows names and adds a search box above the table.
- `SelectColumn::make('verdict')`: a dropdown inside the table. Changing it saves straight away, without opening a form. `->selectablePlaceholder(false)` removes the empty choice.
- `TextColumn::make('reason')->wrap()`: long reasons wrap onto several lines.
- `TextColumn::make('created_at')->since()`: shows "2 minutes ago" instead of a date.
- `->headerActions([CreateAction::make()])`: the **New vote** button at the top of the table. It opens the form in a pop-up and, after saving, shows a small "Created" notification in the corner.
- `->defaultSort('created_at', 'desc')`: newest votes first.
- `->poll('2s')`: reloads the table every 2 seconds, so new votes appear on their own.

**The page** (`getPages()`): the resource has one page, `ManageVotes`, at `/admin/votes`.

---

### ManageVotes.php

```php
<?php

namespace App\Filament\Resources\Votes\Pages;

use App\Filament\Resources\Votes\VoteResource;
use Filament\Resources\Pages\ManageRecords;

class ManageVotes extends ManageRecords
{
    protected static string $resource = VoteResource::class;
}
```
`ManageRecords` is Filament's ready-made page for a "simple" resource: it shows the table and opens the form in a pop-up. The generated file also had a "New vote" button in the page header; I removed it because the button now lives in the table (`headerActions`), so the QR link `?tableAction=create` can open it directly.

In this demo there is no edit or delete button (the generated ones were left out on purpose, so the audience can't delete votes). The only way to change a vote is the dropdown in the table.

---

### VoteStats.php

```php
<?php

namespace App\Filament\Widgets;

use App\Models\Vote;
use chillerlan\QRCode\QRCode;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VoteStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '2s';

    protected function getStats(): array
    {
        $qr = (new QRCode)->render(url('/admin/votes?tableAction=create'));

        return [
            Stat::make('Log in: demo@filament.test / filament', str("<img src='{$qr}'>")->toHtmlString()),
            ...collect(Vote::VERDICTS)->map(fn (string $label, string $verdict) => Stat::make($label, Vote::where('verdict', $verdict)->count())),
        ];
    }
}
```
A *widget* is a block on the dashboard. This one is a "stats overview" widget: one block with 4 small cards (Filament calls them stats).

- `$sort = 1`: show this widget first on the dashboard.
- `$pollingInterval = '2s'`: refresh the numbers every 2 seconds.
- `(new QRCode)->render(...)`: turns a link into a QR code and returns it as a `data:image/svg+xml;base64,…` text, an image stored as text that can go straight into `<img src>`. The library (`chillerlan/php-qrcode`) is already installed because Filament itself needs it.
- `url('/admin/votes?tableAction=create')`: the link inside the QR code. It opens the Votes page with the **New vote** form already open (`create` is the name of that button's action). If the phone isn't logged in yet, it goes to the login page first and comes back afterwards.
- `Stat::make('Log in: …', …)`: the first card, with the login details as title and the QR image as value. `"<img src='{$qr}'>"` puts the QR inside an image tag. Filament normally shows HTML as plain characters for safety; `str(...)->toHtmlString()` marks it as safe HTML so the image is drawn (like React's `dangerouslySetInnerHTML`).
- `collect(Vote::VERDICTS)->map(...)`: `collect()` wraps the list in a Laravel *Collection*, which has JavaScript-style methods (`map`, `filter`, `keys`). `map` passes the value first and the key second, so `$label` is `'Yes'` and `$verdict` is `'yes'`. It makes one card per answer.
- `Vote::where('verdict', $verdict)->count()`: counts the votes with that answer (in SQL: `SELECT COUNT(*) FROM votes WHERE verdict = 'yes'`).
- `...`: spreads the 3 cards into the list, like `...array` in JavaScript.

---

### VoteChart.php

```php
<?php

namespace App\Filament\Widgets;

use App\Models\Vote;
use Filament\Widgets\ChartWidget;

class VoteChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Would you use Filament?';

    protected ?string $pollingInterval = '2s';

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        return [
            'datasets' => [[
                'label' => 'Votes',
                'data' => collect(Vote::VERDICTS)->keys()->map(fn (string $verdict) => Vote::where('verdict', $verdict)->count())->all(),
                'backgroundColor' => ['#8f7cf7', '#c5e384', '#52525b'],
            ]],
            'labels' => array_values(Vote::VERDICTS),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
```
A chart widget. Filament draws it in the browser with **Chart.js**: the PHP array returned by `getData()` is turned into JSON and used as Chart.js's `data` object (`labels` + `datasets`).

- `$sort = 2`: show it below the counters. `$columnSpan = 'full'`: use the full width.
- `$heading`: the title above the chart. `$maxHeight`: keeps it from getting too tall.
- `$pollingInterval = '2s'`: redraw with fresh numbers every 2 seconds.
- `'data'`: the number of votes for yes, maybe and no, in that order. `->keys()` takes the keys (`yes`, `maybe`, `no`), `->map(...)` counts each one, and `->all()` turns the result back into a plain list.
- `'backgroundColor'`: one colour per bar (purple, lime, grey).
- `'labels'`: the text under the bars. `array_values(...)` takes the values (`Yes`, `Maybe`, `No`), like `Object.values()`.
- `getType()`: the chart type. Change `'bar'` to `'line'`, save and refresh to see a different chart.

---

### AdminPanelProvider.php

This file configures the whole admin panel. It was generated by `filament:install`; these are the lines that matter:

```php
return $panel
    ->default()
    ->id('admin')
    ->path('admin')                          // the panel lives at /admin
    ->login()                                // adds the login page
    ->brandName('Filament Live')             // name in the top-left corner
    ->darkMode(isForced: true)               // always dark
    ->colors([
        'primary' => Color::hex('#8f7cf7'),  // buttons and highlights in purple
    ])
    ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')  // finds VoteResource
    ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')        // finds VoteStats and VoteChart
```
The `->middleware([...])` lists further down are generated code that runs on every request before the page (like Express middleware): cookies, sessions, form protection and the login check. You don't need to change them.

---

### start.sh

```bash
#!/usr/bin/env bash
# Starts the demo so phones on the same Wi-Fi can open it, and prints the link to use.
cd "$(dirname "$0")"

URL="http://$(ipconfig getifaddr en0 || ipconfig getifaddr en1):8000/admin"
echo ""
echo "  Open this link (the QR code points here): $URL"
echo ""
(sleep 2 && open "$URL") &

PHP_CLI_SERVER_WORKERS=8 php artisan serve --host=0.0.0.0 --port=8000 --no-reload
```
- `#!/usr/bin/env bash`: tells macOS to run this file with bash. `cd "$(dirname "$0")"`: go to the folder the script is in, so it works from anywhere.
- `$(ipconfig getifaddr en0 || ipconfig getifaddr en1)`: asks macOS for this Mac's address on the local network (for example `192.168.0.152`). `en0` is the Wi-Fi on most MacBooks; if it has no address, `||` tries `en1`. `$( … )` runs a command and pastes its output into the text.
- `echo`: prints the link in the terminal, so you can Cmd+click it.
- `(sleep 2 && open "$URL") &`: opens the link in your browser after 2 seconds. The `&` runs it in the background so the server can start. The QR code is built from the address you open, so it must be this network address and not `localhost`.
- `php artisan serve --host=0.0.0.0 --port=8000`: starts the server on port 8000. `0.0.0.0` means "accept connections from the network", so phones on the Wi-Fi can reach it (the default only accepts this laptop).
- `PHP_CLI_SERVER_WORKERS=8 … --no-reload`: `NAME=value command` sets a setting for that one command only, like `PORT=3000 npm start`. Here it runs 8 copies of the server so many phones are served at once. `--no-reload` is required: without it Laravel ignores the 8 and starts only one.

---

## 4. Run the demo

### From this repo
```bash
git clone https://github.com/rafaelahenriques/filament-live-demo.git
cd filament-live-demo
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan make:filament-user --name=Demo --email=demo@filament.test --password=filament
./start.sh
```

### During the talk
1. Run `./start.sh`. It prints the link and opens it in your browser.
2. Log in with the demo account. The dashboard shows the QR code, the counters and the chart.
3. The audience scans the QR code, logs in with the details on the card, and the **New vote** form opens.
4. Press **Ctrl+C** in the terminal to stop.

### Start again with zero votes
```bash
php artisan migrate:fresh
php artisan make:filament-user --name=Demo --email=demo@filament.test --password=filament
```
`migrate:fresh` empties the whole database, including the login account, so the second command recreates it. If `make:filament-user` ever fails with `UNIQUE constraint failed: users.email`, the account already exists and you can just log in.

### If phones can't open the page
- macOS may ask whether PHP can accept incoming connections: click **Allow**.
- Some Wi-Fi networks (for example at universities) block devices from reaching each other. Use your phone's hotspot for the laptop and the audience instead.
