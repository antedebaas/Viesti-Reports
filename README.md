<a name="readme-top"></a>

[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]

<!-- ABOUT THE PROJECT -->
## About The Project

![DMARC-Reports][report-screenshot]

I needed a quick and easy way to view my dmarc reports.
This is a simple symfony project that can read the inbox for new reports and process them.
I you use it you should use it with a special sole-purpose email adress for dmarc reports.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- GETTING STARTED -->
## Getting Started

Setup is pretty easy 

### Prerequisites

* php 8.1 (or higher)
* composer

### Installation

1. Clone the repo
   ```sh
   git clone https://github.com/antedebaas/DMARC-Reports.git
   ```
2. run composer install
   ```sh
   composer install
   ```
3. point the webserver root to the public/ directory
4. point your webbrowser to http(s)://[yourhost]/setup and follow instructions

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- LICENSE -->
## License

Distributed under the MIT License. See `LICENSE.txt` for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- MARKDOWN LINKS & IMAGES -->
[contributors-shield]: https://img.shields.io/github/contributors/antedebaas/DMARC-Reports.svg?style=for-the-badge
[contributors-url]: https://github.com/antedebaas/DMARC-Reports/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/antedebaas/DMARC-Reports.svg?style=for-the-badge
[forks-url]: https://github.com/antedebaas/DMARC-Reports/network/members
[stars-shield]: https://img.shields.io/github/stars/antedebaas/DMARC-Reports.svg?style=for-the-badge
[stars-url]: https://github.com/antedebaas/DMARC-Reports/stargazers
[issues-shield]: https://img.shields.io/github/issues/antedebaas/DMARC-Reports.svg?style=for-the-badge
[issues-url]: https://github.com/antedebaas/DMARC-Reports/issues
[license-shield]: https://img.shields.io/github/license/antedebaas/DMARC-Reports.svg?style=for-the-badge
[license-url]: https://github.com/antedebaas/DMARC-Reports/blob/master/LICENSE.txt
[report-screenshot]: reportscreenshot.png