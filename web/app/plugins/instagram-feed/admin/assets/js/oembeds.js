var sbioembeds_data = {
    nonce: sbi_oembeds.nonce,
    genericText: sbi_oembeds.genericText,
    images: sbi_oembeds.images,
    modal: sbi_oembeds.modal,
    links: sbi_oembeds.links,
    supportPageUrl: sbi_oembeds.supportPageUrl,
    socialWallActivated: sbi_oembeds.socialWallActivated,
    socialWallLinks: sbi_oembeds.socialWallLinks,
    stickyWidget: false,
    facebook: sbi_oembeds.facebook,
    instagram: sbi_oembeds.instagram,
    connectionURL: sbi_oembeds.connectionURL,
    isFacebookActivated: sbi_oembeds.facebook.active,
    facebookInstallBtnText: null,
    fboEmbedLoader: false,
    instaoEmbedLoader: false,
    openFacebookInstaller: false,
    loaderSVG: sbi_oembeds.loaderSVG,
    checkmarkSVG: sbi_oembeds.checkmarkSVG,
    timesCircleSVG: sbi_oembeds.timesCircleSVG,
    installerStatus: null
}

var sbioEmbeds = new Vue({
    el: "#sbi-oembeds",
    http: {
        emulateJSON: true,
        emulateHTTP: true
    },
    data: sbioembeds_data,
    methods: {
        openFacebookllModal: function () {
            this.openFacebookInstaller = true
        },
        closeModal: function () {
            this.openFacebookInstaller = false
        },
        isoEmbedsEnabled: function () {
            if (this.facebook.doingOembeds && this.instagram.doingOembeds) {
                return true;
            }

        },
        FacebookShouldInstallOrEnable: function () {
            // if the plugin is activated and installed then just enable oEmbed
            if (this.isFacebookActivated) {
                this.enableFacebookOembed();
                return;
            }
            // if the plugin is not activated and installed then open the modal to install and activate the plugin
            if (!this.isFacebookActivated) {
                this.openFacebookllModal();

            }
        },
        installFacebook: function () {
            this.installerStatus = 'loading';
            let data = new FormData();
            data.append('action', sbi_oembeds.facebook.installer.action);
            data.append('nonce', sbi_oembeds.nonce);
            data.append('plugin', sbi_oembeds.facebook.installer.plugin);
            data.append('type', 'plugin');
            data.append('referrer', sbi_oembeds.facebook.installer.referrer);

            fetch(sbi_oembeds.ajax_handler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success == false) {
                        this.installerStatus = 'error'
                    }
                    if (data.success == true) {
                        this.isFacebookActivated = true;
                        this.installerStatus = 'success'
                    }
                    if (typeof data.data === 'object') {
                        this.facebookInstallBtnText = data.data.msg;
                    } else {
                        this.facebookInstallBtnText = data.data;
                    }
                    setTimeout(function () {
                        this.installerStatus = null;
                    }.bind(this), 3000);

                });
        },
        enableInstaoEmbed: function () {
            this.instaoEmbedLoader = true;

            let oembedConnectUrl = this.connectionURL.connect,
                appendURL = this.connectionURL.stateURL;

            const urlParams = {
                'sbi_con': this.connectionURL.sbi_con,
                'state': "{'{url=" + appendURL + "}'}"
            }

            let form = document.createElement('form');
            form.setAttribute('method', 'post');
            form.setAttribute('action', oembedConnectUrl);

            for (const key in urlParams) {
                let hiddenField = document.createElement('input');
                hiddenField.setAttribute('type', 'hidden');
                hiddenField.setAttribute('name', key);
                hiddenField.setAttribute('value', urlParams[key]);
                form.appendChild(hiddenField);
            }

            document.body.appendChild(form);
            form.submit();
        },
        enableFacebookOembed: function () {
            this.fboEmbedLoader = true;
            let oembedConnectUrl = this.connectionURL.connect,
                appendURL = this.connectionURL.stateURL;

            const urlParams = {
                'sbi_con': this.connectionURL.sbi_con,
                'state': "{'{url=" + appendURL + "}'}"
            }

            let form = document.createElement('form');
            form.setAttribute('method', 'post');
            form.setAttribute('action', oembedConnectUrl);

            for (const key in urlParams) {
                let hiddenField = document.createElement('input');
                hiddenField.setAttribute('type', 'hidden');
                hiddenField.setAttribute('name', key);
                hiddenField.setAttribute('value', urlParams[key]);
                form.appendChild(hiddenField);
            }

            document.body.appendChild(form);
            form.submit();
        },
        disableFboEmbed: function () {
            this.fboEmbedLoader = true;
            let data = new FormData();
            data.append('action', 'disable_facebook_oembed_from_instagram');
            data.append('nonce', this.nonce);
            fetch(sbi_oembeds.ajax_handler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success == true) {
                        this.fboEmbedLoader = false;
                        this.facebook.doingOembeds = false;
                        // get the updated connection URL after disabling oEmbed
                        this.connectionURL = data.data.connectionUrl;
                    }

                });
        },
        disableInstaoEmbed: function () {
            this.instaoEmbedLoader = true;
            let data = new FormData();
            data.append('action', 'disable_instagram_oembed_from_instagram');
            data.append('nonce', this.nonce);
            fetch(sbi_oembeds.ajax_handler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success == true) {
                        this.instaoEmbedLoader = false;
                        this.instagram.doingOembeds = false;
                        // get the updated connection URL after disabling oEmbed
                        this.connectionURL = data.data.connectionUrl;
                    }

                });
        },
        installButtonText: function (buttonText = null) {
            if (buttonText) {
                return buttonText;
            } else if (this.facebook.installer.nextStep == 'free_install') {
                return this.modal.install;
            } else if (this.facebook.installer.nextStep == 'free_activate') {
                return this.modal.activate;
            }
        },
        installIcon: function () {
            if (this.isFacebookActivated) {
                return;
            }
            if (this.installerStatus == null) {
                return this.modal.plusIcon;
            } else if (this.installerStatus == 'loading') {
                return this.loaderSVG;
            } else if (this.installerStatus == 'success') {
                return this.checkmarkSVG;
            } else if (this.installerStatus == 'error') {
                return this.timesCircleSVG;
            }
        },

        /**
         * Toggle Sticky Widget view
         *
         * @since 4.0
         */
        toggleStickyWidget: function () {
            this.stickyWidget = !this.stickyWidget;
        },
    },
    created() {
        // Display the "Install" button text on modal depending on condition
        if (this.facebook.installer.nextStep == 'free_install') {
            this.facebookInstallBtnText = this.modal.install;
        } else if (this.facebook.installer.nextStep == 'free_activate' || this.facebook.installer.nextStep == 'pro_activate') {
            this.facebookInstallBtnText = this.modal.activate;
        }
    }
})