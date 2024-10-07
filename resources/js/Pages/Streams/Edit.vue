<template>
    <div>

        <Head :title="form.title" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/streams">Streams</Link>
            <span class="text-indigo-400 font-medium">/</span>
            {{ form . title }}
        </h1>
        <div class="flex flex-col space-y-4 lg:flex-row lg:space-y-0 lg:space-x-4">
            <div class="w-full lg:w-1/2 max-w-3xl bg-white rounded-md shadow overflow-hidden">
          <form @submit.prevent="update">
              <div class="flex flex-wrap -mb-8 -mr-6 p-8">
            <text-input v-model="form.title" :error="form.errors.title" class="pb-8 pr-6 w-full lg:w-1/2"
                label="Name" />
            <text-input v-model="form.uuid" :error="form.errors.uuid" class="pb-8 pr-6 w-full lg:w-1/2"
                label="UUID" />
            <text-input v-model="form.description" :error="form.errors.description"
                class="pb-8 pr-6 w-full lg:w-1/2" label="Description" />
            <text-input v-model="form.slug" :error="form.errors.slug" class="pb-8 pr-6 w-full lg:w-1/2"
                label="Slug" />
            <select-input v-model="form.endpoint" :error="form.errors.endpoint"
                class="pb-8 pr-6 w-full lg:w-1/2" label="Endpoint">
                <option value="" disabled></option>
                <option v-for="endpoint in endpoints" :key="endpoint.id" :value="endpoint.id"
              :selected="endpoint.id == form.endpoint">{{ endpoint . title }}</option>
            </select-input>
              </div>
              <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
            <button class="text-red-600 hover:underline" tabindex="-1" type="button"
                @click="destroy">Delete Stream</button>
            <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Update
                Stream</loading-button>
              </div>
          </form>
            </div>
            <div class="w-full lg:w-1/2 max-w-3xl bg-white rounded-md shadow overflow-hidden">
          <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
              <button class="btn-indigo" tabindex="-1" type="button" @click="loadStream">Start
            Stream</button>
          </div>
          <div class="flex flex-wrap -mb-8 -mr-6 p-8">
              <video class="w-full h-64" controls>
            <source type="video/mp4">
            Your browser does not support the video tag.
              </video>
          </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {
        Head,
        Link
    } from '@inertiajs/vue3'
    import Icon from '@/Shared/Icon.vue'
    import Layout from '@/Shared/Layout.vue'
    import TextInput from '@/Shared/TextInput.vue'
    import SelectInput from '@/Shared/SelectInput.vue'
    import LoadingButton from '@/Shared/LoadingButton.vue'
    import TrashedMessage from '@/Shared/TrashedMessage.vue'

    export default {
        components: {
            Head,
            Icon,
            Link,
            LoadingButton,
            SelectInput,
            TextInput,
            TrashedMessage,
        },
        layout: Layout,
        props: {
            stream: Object,
            endpoints: Array,
        },
        remember: 'form',
        data() {
            return {
                form: this.$inertia.form({
                    id: this.stream.id,
                    title: this.stream.title,
                    uuid: this.stream.uuid,
                    slug: this.stream.slug,
                    description: this.stream.description,
                    endpoint: this.stream.endpoint.id,
                }),
            }
        },
        methods: {
            update() {
                this.form.put(`/streams/${this.stream.id}`)
            },
            destroy() {
                if (confirm('Are you sure you want to delete this stream?')) {
                    this.$inertia.delete(`/streams/${this.stream.id}`)
                }
            },
            restore() {
                if (confirm('Are you sure you want to restore this stream?')) {
                    this.$inertia.put(`/streams/${this.stream.id}/restore`)
                }
            },
            loadStream() {

              //Clear Ice Candidates from local storage
                localStorage.removeItem('iceCandidates');

                let videoElement = document.querySelector('video');

                let config = {
                    iceServers: [{
                        urls: ["stun:stun.l.google.com:19302"]
                    }]
                };

                let stream = new MediaStream();
                let pc = new RTCPeerConnection(config);

                pc.ontrack = (event) => {
                    stream.addTrack(event.track);
                    videoElement.srcObject = stream;
                    videoElement.play();
                };

                pc.addTransceiver('video', {
                    'direction': 'sendrecv'
                });

                const getRemoteSdp = async (formData) => {
                    let response = await fetch(`/stream/getRemoteSdp`, {
                        method: 'POST',
                        body: formData
                    });
                    let data = await response.json();
                    //Add session id to video element
                    videoElement.setAttribute('video_session', data.session_id);
                    let answer = new RTCSessionDescription({
                        type: 'answer',
                        sdp: atob(data.sdp)
                    });
                    await pc.setRemoteDescription(answer);
                };
                pc.onnegotiationneeded = async () => {
                    try {
                        let offer = await pc.createOffer();
                        await pc.setLocalDescription(offer);
                        let formData = new FormData();
                        formData.append('stream_uuid', this.stream.uuid);
                        formData.append('data', btoa(offer.sdp));
                        await getRemoteSdp(formData);
                    } catch (err) {
                        console.error(err);
                    }
                };

                pc.onicecandidate = async (event) => {
                    if (event.candidate) {
                      //Save Ice Candidates to local storage
                        let iceCandidates = JSON.parse(localStorage.getItem('iceCandidates')) || [];
                        iceCandidates.push(btoa(event.candidate));
                        localStorage.setItem('iceCandidates', JSON.stringify(iceCandidates));
                    }
                };

                pc.oniceconnectionstatechange = (event) => {
                    if (pc.iceConnectionState === 'disconnected') {
                        //Remove Ice Candidates from local storage
                        localStorage.removeItem('iceCandidates');
                    }
                };

                pc.onicegatheringstatechange = (event) => {
                    if (pc.iceGatheringState === 'complete') {
                        //Save Ice Candidates to local storage
                        let iceCandidates = JSON.parse(localStorage.getItem('iceCandidates')) || [];
                        let session_id = videoElement.getAttribute('video_session');
                        let formData = new FormData();
                        formData.append('session_id', session_id);
                        formData.append('stream_uuid', this.stream.uuid);
                        formData.append('candidates', JSON.stringify(iceCandidates));
                        fetch(`/stream/saveIceCandidates`, {
                            method: 'POST',
                            body: formData
                        });
                    }
                };
            },

        },
    }
</script>
