Uncertainties:

	* There's no relationships but it's requested in the requirements. The would normally be a question here of cardinality. I've kept it one (league) to many (teams) but there might be scenarios where a team can belong to more than one league.
	* Strip (should be kit) isn't explained. I would normally ask about this. It could be home/away but that would belong to match not team. It could also be a list of colours (possibly top to bottom) though kits can be quite complex. I'm not sure if there are rules for kits allowing a simplification. Could be a list of colours, subdivisions and patterns. Could also be an SVG of the kit. You would also need more than one kit (is it home or away).

	The data types being entirely up to me simplify this though I'd normally ask rather than guess.

	In this case I'm going to interpret strip as "colours" and will use serialization as the type.

	In some cases this is a bad practice (denormalization). The primary reason for that is for non-atomic data running queries in relation to it becomes very difficult. It may also become impossible to index it usefully and you'll not be able to rely on the database layer either to validate it as stringently (application layer instead).
	None of the requirements however require the use of the column in searches so in this case I deem it would be appropriate to use object serialization. 
	Some object/document databases do support serialized data (even the latest version of MySQL now supports JSON) to varying extents though that's unlikely to include support for PHP's serialization format.
	I've not investigated the possibility of Doctrine and PHP providing a convenient way to serialize and deserialize as JSON.

	Although the requirements ask to update all attributes I've not included id in that.

	The API output format isn't specified. I'm going to stick to JSON.

Platform:

	I'll use docker latest and I'll use sqlite to save on a docker image.

	Using centos 7 as the host.

Host setup:

	I've used docker although you can also use php7.2 with sqlite3 and composer latest if it's on your system.
	I've not investigated all of the requirements needed in respect to PHP configuration and modules required for this.
	I've relied on the defaults from the docker image.

	Docker:

		yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
		yum install docker-ce
		# Note: This won't enable docker on restart.
		systemctl start docker
		# Note: This is used as an all in one build and run image. In practice it's better the two be separated, production should not be in the business of building nor carry the extra baggage. For the purposes of demonstration however, this keeps it simple.
		docker pull composer

General notes and quirks:

	* I've tried to use symfony maker for some of the doctrine classes. It gives a nice bit of boiler plate but leaves a lot to be desired. It doesn't let you set unique constraints for example, spams some code you don't need, etc.
	* Although it's suggest to make a migration from the start. There's some debate on how to manage migrations and flattening them. It's not clear in the tutorial whether it's asking for a migration simply to show how to do it or if it has another intention.
	* I'm not sure what doctrine is doing be default for the relations. I'd expect it to be lazy loading though.
	* I'm not sure how doctrine is implementing orphan removal (database layer with cascade or application).
	* It should be possible to have services automatically register themselves (route, input, etc) as well as providing a means of advertizing the API though I've not looked into this with Symfony.
	* HttpJsonTransport is a horrible placeholder to be able to implement a better system of negotiating different transport schemes.
	* I've been very light on the PHPDoc (almost none).
	* The JWT authentication is just a demonstration, there's no user handling there and the token's not very useful without that embedded.

Application setup (after git clone and moving to directory):

	Checkout:

		Git clone and cd into the directory.

	If using docker (composer image):

		# Only if using docker, make sure you use a fresh shell that you can close after to clear the changes. Unalias first if you already have one for composer.
		# Note: I don't usually rely on aliases but it's here for convenience.
		alias composer="docker run --rm -p 8001:8001 -it -v \"$PWD\"/app:/app composer"
		alias console="composer bin/console"

	Otherwise:

		alias console="bin/console"

	Setup:

		cd app
		cp .env.local{.dist,}
		composer install
		console doctrine:schema:create
		console doctrine:fixtures:load

	Web:

		console server:run 0.0.0.0:8001
