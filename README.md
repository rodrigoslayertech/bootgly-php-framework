<p align="center">
  <img src="https://github.com/bootgly/.github/raw/main/favicon-temp1-128.png" alt="bootgly-logo" width="120px" height="120px"/>
</p>
<h1 align="center">Bootgly</h1>
<p align="center">
  <i>Base PHP Framework for Multi Projects.</i>
</p>
<p align="center">
  <a href="https://packagist.org/packages/bootgly/bootgly">
    <img alt="Bootgly License" src="https://img.shields.io/github/license/bootgly/bootgly"/>
    </br>
    <img alt="Github Actions - Bootgly Workflow" src="https://img.shields.io/github/actions/workflow/status/bootgly/bootgly/bootgly.yml?label=bootgly"/>
    <img alt="Github Actions - Docker Workflow" src="https://img.shields.io/github/actions/workflow/status/bootgly/bootgly/docker.yml?label=docker"/>
  </a>
</p>

> Bootgly is the first PHP framework to use the [I2P (Interface-to-Platform) architecture][I2P_ARQUITECTURE].

🚧

DO NOT USE IT IN PRODUCTION ENVIRONMENTS.

Bootgly is in testing.
A stable release is planned for December 2023.

[Documentation is under construction][PROJECT_DOCS].

🚧

## Table of Contents

- [About](#-about)
- [Boot](#-boot)
  - [Compatibility](#-compatibility)
  - [Dependencies](#️-dependencies)
- [Community](#-community)
  - [Contributing](#-contributing)
  - [Code of Conduct](#-code-of-conduct)
  - [Social Networks](#-social-networks)
  - [Sponsorship](#-sponsorship)
- [Compliances](#-compliances)
  - [License](#-license)
  - [Versioning](#-versioning)
- [Highlights](#-highlights)
- [Usage](#-usage)

---

## 🤔 About

Bootgly is a base framework for developing APIs and Apps for both CLI (Console) 📟 and WPI (Web) 🌐.

Focused on **efficiency**, for adopting a minimum dependency policy.

Due to this policy, its unique I2P architecture, and some unusual code conventions and design patterns, Bootgly has superior **performance** and **versatility**, and has **easy-to-understand Code API**.

### Bootgly CLI 📟

> Command Line Interface

Interface: [CLI][CLI_INTERFACE]

Platform: [Console][CONSOLE_PLATFORM] (TODO)

Terminal components |
--- |
[Alert component][CLI_TERMINAL_ALERT] | 
[Field component][CLI_TERMINAL_FIELD] | 
[Menu component][CLI_TERMINAL_MENU] | 
[Progress component][CLI_TERMINAL_PROGRESS] | 
[Table component][CLI_TERMINAL_TABLE] | 

CLI components |
--- |
[Header component][CLI_HEADER] | 

### Bootgly WPI 🌐

> Web Programming Interface 

Interface: [WPI][WPI_INTERFACE]

Platform: [Web][WEB_PLATFORM] (IN DEVELOPMENT)

Web interfaces | Web nodes
--- | ---
[TCP Client][WEB_TCP_CLIENT_INTERFACE] | HTTP Client CLI (🤔)
[TCP Server][WEB_TCP_SERVER_INTERFACE] | [HTTP Server CLI][WEB_HTTP_SERVER_CLI]
UDP Client (🤔) | WS Client
UDP Server (🤔) | WS Server

-- 

🤔 = TODO

---

## 🟢 Boot

### 🤝 Compatibility

Operation System |
--- |
✅ Linux (Debian based) |
❌ Windows |
❔ Unix |

--

✅ = Compatible

❌ = Incompatible

❔ = Untested

Above is the native compatibility, of course it is possible to run on Windows and Unix using containers.

### ⚙️ Dependencies

- PHP 8.2+ ⚠️
- Opcache with JIT enabled (+50% performance) 👍

#### \- Bootgly CLI 📟
- `php-cli` ⚠️
- `php-readline` ⚠️

#### \- Bootgly WPI 🌐

##### CLI + WPI *API ¹ (eg. Bootgly HTTP Server CLI):
- \* See Bootgly CLI dependencies \*

##### WPI in Non-CLI (apache2handler, litespeed and nginx) SAPI ²:
- `rewrite` module enabled ⚠️

--

⚠️ = Required

👍 = Recommended

¹ *API = Can be Server API (SAPI), Client API (CAPI), etc.
² SAPI = Server API

---

## 🌱 Community

Join us and help the community.

**Love Bootgly? Give [our repo][GITHUB_REPOSITORY] a star ⭐!**

### 💻 Contributing

Wait for the "contributing guidelines" to start your contribution.

#### 🛂 Code of Conduct

Help us keep Bootgly open and inclusive. Please read and follow our [Code of Conduct][CODE_OF_CONDUCT].

### 🔗 Social networks
- Bootgly on **LinkedIn**: [[Company Page][LINKEDIN]]
- Bootgly on **Telegram**: [[Telegram Group][TELEGRAM]]
- Bootgly on **Reddit**: [[Reddit Community][REDDIT]]
- Bootgly on **Discord**: [[Discord Channel][DISCORD]]

### 💖 Sponsorship

A lot of time and energy is devoted to Bootgly projects. To accelerate your growth, if you like this project or depend on it for your stack to work, consider [sponsoring it][GITHUB_SPONSOR].

Your sponsorship will keep this project always **up to date** with **new features** and **improvements** / **bug fixes**.

---

## 📝 Compliances

### 📃 License

The Bootgly is open-sourced software licensed under the [MIT license][MIT_LICENSE].

### 📑 Versioning

Bootgly uses [Semantic Versioning 2.0][SEMANTIC_VERSIONING].

---

## 🖼 Highlights

### \- Bootgly CLI 📟

| ![HTTP Server CLI started - Initial output](https://github.com/bootgly/.github/raw/main/screenshots/bootgly-php-framework/Bootgly-Progress-Bar-component.png "Render 6x faster than Symfony / Laravel") |
|:--:| 
| *Progress component (with Bar) - Render 6x faster than Symfony / Laravel* |

### \- Bootgly WPI 🌐

| ![HTTP Server CLI - Faster than Workerman +7%](https://github.com/bootgly/.github/raw/main/screenshots/bootgly-php-framework/Server-CLI-HTTP-Benchmark-Ryzen-9-3900X-WSL2.png "HTTP Server CLI - +7% faster than Workerman (Plain Text test)'") |
|:--:| 
| *HTTP Server CLI - +7% faster than [Workerman](https://www.techempower.com/benchmarks/#section=data-r21&test=plaintext&l=zik073-6bj) (Plain Text test)* |

| ![HTTP Server CLI - started in Monitor mode](https://github.com/bootgly/.github/raw/main/screenshots/bootgly-php-framework/Bootgly_WPI_-_HTTP_Server_CLI.png "HTTP Server CLI - started in Monitor mode") |
|:--:| 
| HTTP Server CLI - started in `monitor` mode


More **Screenshots**, videos and details can be found in the home page of [Bootgly Docs][PROJECT_DOCS].

---

## 🔧 Usage

### 📟 Bootgly CLI:

<details>
  <summary><b>Run CLI demo</b></summary><br>

  1) See the examples in `projects/Bootgly/CLI/examples/`;
  2) Check the file `projects/Bootgly/CLI.php`;
  3) Run the Bootgly CLI demo in terminal:

  ```bash
  php bootgly demo
  ```
</details>

<details>
  <summary><b>Setup Bootgly CLI globally (on /usr/local/bin)</b></summary><br>

  1) Run the Bootgly CLI setup command in terminal (with sudo):

  ```bash
  sudo php bootgly setup
  ```
</details>

<details>
  <summary><b>Perform Bootgly tests</b></summary><br>

  1) Check the bootstrap tests file `tests/@.php`;
  2) Run the Bootgly CLI test command in terminal:

  ```bash
  bootgly test
  ```
</details>

### 🌐 Bootgly WPI:

<details>
  <summary><b>Running a HTTP Server</b></summary>

  ##### **Option 1: Non-CLI SAPI (Apache, LiteSpeed, Nginx, etc)**

  1) Enable support to `rewrite`;
  2) Configure the WPI constructor in `projects/Bootgly/WPI.php` file;
  3) Run the Non-CLI HTTP Server pointing to `index.php`.

  ##### **Option 2: CLI SAPI**

  Directly in Linux OS *(max performance)*:

  1) Configure the Bootgly HTTP Server script in `scripts/http-server-cli` file;
  2) Configure the HTTP Server API in `projects/Bootgly/WPI/HTTP-Server.API.php` file;
  3) Run the Bootgly HTTP Server CLI in the terminal:

  ```bash
  bootgly serve
  ```
  or
  ```bash
  php scripts/http-server-cli
  ```

  --

  or using Docker:

  1) Pull the image:

  ```bash
  docker pull bootgly/http-server-cli
  ```

  2) Run the container in interactive mode and in the host network for max performance:

  ```bash
  docker run -it --network host bootgly/http-server-cli
  ```
</details>

<details>
  <summary><b>Routing HTTP Requests with Bootgly HTTP Server Router</b></summary><br>

  [The Router][HTTP_SERVER_ROUTER_CLASS] for HTTP Servers provides a flexible and powerful web routing system. The `route` method is used to route routes, with the schema as follows:

  ```php
  route (string $route, \Closure|callable $handler, null|string|array $condition = null) : bool
  ```

  - `$route` is the URL pattern to match that accepts params.
  - `$handler` is the callback to be executed when the route is matched.
  - `$condition` is the HTTP method(s) that this route should respond to.

  **Basic Usage**

  ```php
  $Router->route('/', function ($Response, $Request, $Route) {
    echo 'Hello World!';
  }, GET);
  ```

  Handler arguments:
  - `$Response` is the HTTP Server Response
  - `$Request` is the HTTP Server Request
  - `$Route` is the Route matched

  <!-- (WIP) "I commented this because some things are going to change soon."
  ## Some examples

  **1. Route Callbacks**

  ```php
  $Router->route('/', function () {echo 'Hello World!';}, GET); // Closure
  $Router->route('/hello', ['talk', 'world'], GET); // Function
  $Router->route('/world', ['HelloWorld::talk'], GET); // Static Class
  ```

  **2. Route with Parameters**

  ```php
  $Router->route('/user/:id', function () use ($Route) {
    echo 'User ID: ' . $Route->Params->id;
  }, GET);
  ```

  **3. Route with Multiple Methods**

  ```php
  $Router->route('/data', function () {
    echo 'Data!';
  }, [GET, POST]);
  ```

  **4. Nested Routes**

  ```php
  $Router->route('/profile/:*', function () use ($Router, $Route) {
    echo 'Hello ';

    $Router->route('user/:id', function () use ($Route) {
        echo 'User ID: ' . $Route->Params->id;
    });
  }, GET);
  ```

  **5. Catch-All Route**

  ```php
  $Router->route('/*', function ($Response) {
    $Response->code = 404;
    $Response('pages/404')->send();
  });
  ```
  -->
</details>



<!-- Links -->
[I2P_ARQUITECTURE]: https://docs.bootgly.com/manual/Bootgly/basic/architecture/overview
[CLI_INTERFACE]: https://github.com/bootgly/bootgly/tree/main/Bootgly/CLI/
[CLI_TERMINAL_COMPONENTS]: https://github.com/bootgly/bootgly/tree/main/Bootgly/CLI/Terminal/components

[CLI_TERMINAL_ALERT]: https://github.com/bootgly/bootgly/tree/main/Bootgly/CLI/Terminal/components/Alert
[CLI_TERMINAL_FIELD]: https://github.com/bootgly/bootgly/tree/main/Bootgly/CLI/Terminal/components/Field
[CLI_TERMINAL_MENU]: https://github.com/bootgly/bootgly/tree/main/Bootgly/CLI/Terminal/components/Menu
[CLI_TERMINAL_PROGRESS]: https://github.com/bootgly/bootgly/tree/main/Bootgly/CLI/Terminal/components/Progress
[CLI_TERMINAL_TABLE]: https://github.com/bootgly/bootgly/tree/main/Bootgly/CLI/Terminal/components/Table
[CLI_HEADER]: https://github.com/bootgly/bootgly/tree/main/Bootgly/CLI/components/Header.php
[CONSOLE_PLATFORM]: https://github.com/bootgly/bootgly-console

[WPI_INTERFACE]: https://github.com/bootgly/bootgly/tree/main/Bootgly/WPI/
[HTTP_SERVER_ROUTER_CLASS]: https://github.com/bootgly/bootgly/blob/main/Bootgly/WPI/Modules/HTTP/Server/Router.php
[WEB_TCP_CLIENT_INTERFACE]: https://github.com/bootgly/bootgly/blob/main/Bootgly/WPI/Interfaces/TCP/Client.php
[WEB_TCP_SERVER_INTERFACE]: https://github.com/bootgly/bootgly/blob/main/Bootgly/WPI/Interfaces/TCP/Server.php
[WEB_HTTP_SERVER_CLI]: https://github.com/bootgly/bootgly/blob/main/Bootgly/WPI/Nodes/HTTP/Server/CLI.php
[WEB_PLATFORM]: https://github.com/bootgly/bootgly-web


[PROJECT_DOCS]: https://docs.bootgly.com/
[GITHUB_REPOSITORY]: https://github.com/bootgly/bootgly/
[GITHUB_SPONSOR]: https://github.com/sponsors/bootgly/

[TELEGRAM]: https://t.me/bootgly/
[REDDIT]: https://www.reddit.com/r/bootgly/
[DISCORD]: https://discord.com/invite/SKRHsYmtyJ/
[LINKEDIN]: https://www.linkedin.com/company/bootgly/


[CODE_OF_CONDUCT]: CODE_OF_CONDUCT.md
[SEMANTIC_VERSIONING]: https://semver.org/


[MIT_LICENSE]: https://opensource.org/license/mit/
