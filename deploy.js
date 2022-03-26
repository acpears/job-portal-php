const FtpDeploy = require("ftp-deploy")
const ftp = require("basic-ftp")
const basicFtpClient = new ftp.Client()
const https = require('https');

const LOCAL_BUILD_DIRECTORY = "admin_views"
const DEPLOY_DIRECTORY_NAME = "temp"
const PRODUCTION_DIRECTORY_NAME = "master"
const BROKEN_BUILD = "broken"
const PRODUCTION_URL = "https://akjaw.com/"

const [host, port, user, password] = process.argv.slice(2)

function uploadBuildDirectory() {
    const config = {
        host,
        port,
        user,
        password,
        deleteRemote: false,
        localRoot: `./`,
        remoteRoot: `./ftp_test`,
        include: ["*.php", "**/*.php"],

    }

    const ftpDeploy = new FtpDeploy()

    ftpDeploy.on("uploading", data => {
        console.log('Debug');
        const { totalFilesCount, transferredFileCount, filename } = data
        console.log(`${transferredFileCount} out of ${totalFilesCount} ${filename}`)
    })

    ftpDeploy.on("upload-error", data => {
        console.log(data.err)
        throw new Error("Upload FAILED")
    })

    return ftpDeploy
        .deploy(config)
        .then(() => console.log(`Upload COMPLETED`))
}

function onError(name, error) {
    console.log(`${name} FAILED`)
    console.log(error)
    fail()
}

function fail() {
    basicFtpClient.close()
    process.exit(1)
}

function main() {
    return uploadBuildDirectory()
        .catch(error => onError("Upload", error))
        .then(() => console.log("Good"))
}

main();