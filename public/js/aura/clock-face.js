(() => {
    const state = {
        models: null,
        stream: null,
        watchId: null,
        lastSent: 0,
        component: null,
    };

    const config = () => {
        const root = document.getElementById('aura-clock-app');

        return {
            script: root?.dataset.script ?? '',
            model: root?.dataset.model ?? '',
        };
    };

    const setStatus = (text) => {
        const status = document.getElementById('aura-clock-face-status');

        if (status && text) {
            status.textContent = text;
        }
    };

    const loadScript = (url) => new Promise((resolve, reject) => {
        if (window.faceapi) {
            resolve();

            return;
        }

        const script = document.createElement('script');
        script.src = url;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('script'));
        document.head.appendChild(script);
    });

    const ensureModels = async () => {
        const { script, model } = config();

        if (! state.models) {
            state.models = (async () => {
                await loadScript(script);
                await Promise.all([
                    window.faceapi.nets.tinyFaceDetector.loadFromUri(model),
                    window.faceapi.nets.faceLandmark68Net.loadFromUri(model),
                    window.faceapi.nets.faceRecognitionNet.loadFromUri(model),
                ]);
            })();
        }

        return state.models;
    };

    const startCamera = async () => {
        const video = document.getElementById('aura-clock-video');

        if (! video || ! navigator.mediaDevices?.getUserMedia) {
            throw new Error('camera');
        }

        if (! state.stream) {
            state.stream = await navigator.mediaDevices.getUserMedia({
                audio: false,
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
            });
        }

        if (video.srcObject !== state.stream) {
            video.srcObject = state.stream;
            await video.play();
        }
    };

    const detect = async () => {
        const root = document.getElementById('aura-clock-app');
        setStatus(root?.dataset.scanning ?? '');
        await ensureModels();
        await startCamera();

        const detection = await window.faceapi
            .detectSingleFace(
                document.getElementById('aura-clock-video'),
                new window.faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.5 }),
            )
            .withFaceLandmarks()
            .withFaceDescriptor();

        const descriptor = detection ? Array.from(detection.descriptor) : null;
        stopCamera();

        return descriptor;
    };

    const stopCamera = () => {
        state.stream?.getTracks().forEach((track) => track.stop());
        state.stream = null;

        const video = document.getElementById('aura-clock-video');

        if (video) {
            video.srcObject = null;
        }
    };

    const trackingOn = () => document.getElementById('aura-clock-app')?.dataset.tracking === '1';

    const showCount = (count) => {
        const countNode = document.getElementById('aura-clock-track-count');

        if (countNode && count) {
            countNode.textContent = String(count);
        }
    };

    const locate = () => new Promise((resolve, reject) => {
        if (! navigator.geolocation) {
            reject(new Error('unavailable'));

            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => resolve(position),
            () => reject(new Error('denied')),
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 },
        );
    });

    window.auraClockFace = {
        begin(component) {
            if (component) {
                state.component = component;
            }

            if (state.watchId !== null || ! state.component || ! navigator.geolocation) {
                return;
            }

            state.watchId = navigator.geolocation.watchPosition(
                async (position) => {
                    if (! trackingOn()) {
                        this.end();

                        return;
                    }

                    const now = Date.now();

                    if (now - state.lastSent < 30000) {
                        return;
                    }

                    state.lastSent = now;

                    const count = await state.component.track(
                        position.coords.latitude,
                        position.coords.longitude,
                        position.coords.accuracy,
                    );

                    showCount(count);
                },
                () => {},
                { enableHighAccuracy: true, maximumAge: 15000, timeout: 20000 },
            );
        },

        end() {
            if (state.watchId !== null && navigator.geolocation) {
                navigator.geolocation.clearWatch(state.watchId);
            }

            state.watchId = null;
        },

        async preview(component) {
            const root = document.getElementById('aura-clock-app');

            if (trackingOn()) {
                setStatus(root?.dataset.trackingLabel ?? '');
                this.begin(component);

                return;
            }

            try {
                setStatus(root?.dataset.loading ?? '');
                await ensureModels();
                await startCamera();
                setStatus(root?.dataset.ready ?? '');
            } catch (error) {
                setStatus('');
            }
        },

        async enroll(component) {
            try {
                const descriptor = await detect();

                if (! descriptor) {
                    await component.faceNotFound();

                    return;
                }

                await component.enroll(descriptor);
            } catch (error) {
                await component.cameraDenied();
            }
        },

        async clock(component, method) {
            try {
                const descriptor = await detect();

                if (! descriptor) {
                    await component.faceNotFound();

                    return;
                }

                const root = document.getElementById('aura-clock-app');
                setStatus(root?.dataset.locating ?? '');

                let position;

                try {
                    position = await locate();
                } catch (error) {
                    if (error.message === 'unavailable') {
                        await component.locationUnavailable();
                    } else {
                        await component.locationDenied();
                    }

                    return;
                }

                const tracking = await component[method](
                    position.coords.latitude,
                    position.coords.longitude,
                    position.coords.accuracy,
                    descriptor,
                );

                if (tracking) {
                    const root = document.getElementById('aura-clock-app');

                    if (root) {
                        root.dataset.tracking = '1';
                    }

                    setStatus(root?.dataset.trackingLabel ?? '');
                    this.begin(component);
                } else {
                    const root = document.getElementById('aura-clock-app');

                    if (root) {
                        root.dataset.tracking = '0';
                    }

                    this.end();
                }
            } catch (error) {
                await component.cameraDenied();
            }
        },
    };
})();
