<template>
  <aside class="chessboard_manager">
    <section class="chessboard_settings">
      <h3 class="title">Settings</h3>
      <label>
        <input type="number" min="1" max="8" size="2" v-model.number="settings.boardSize" />
        Board size
      </label>
    </section>

    <section class="chessboard_results">
      <h3 class="title">Results</h3>

      <Loader v-if="resultsLoading" />
      <div v-if="results">
        <dl class="server_response">
          <dt>isValidBoard</dt>
          <dd :class="results.isValidBoard ? 'success' : 'failure'">{{ results.isValidBoard }}</dd>
          <dt>Boardsize</dt>
          <dd>{{results.boardSize}}</dd>
          <dt>ComputationTime</dt>
          <dd>{{results.computationTime}}ms</dd>
          <dt>Solutions</dt>
          <dd>{{results.numberOfSolutions}}</dd>
        </dl>

        <h4>Drag the slider to see the solutions
          <vue-slider
            :value="resultSolutionPreviewIndex"
            :max="results.numberOfSolutions-1"
            @change="value => $emit('preview-solution-change', value)"
          />
        </h4>

      </div>

      <div class="results_actionbar" v-if="!resultsLoading">
        <button @click="$emit('calculate-click')">Calculate</button>
        <button @click="$emit('reset-click')">Reset</button>
      </div>
    </section>
  </aside>
</template>
<script>
import VueSlider from 'vue-slider-component';
import Loader from './Loader.vue';
import 'vue-slider-component/theme/antd.css';


export default {
  props: {
    settings: {
      type: Object,
      required: true,
    },
    results: {
      type: Object,
      required: false,
    },
    resultsLoading: {
      type: Boolean,
      required: true,
    },
    resultSolutionPreviewIndex: {
      type: Number,
      required: true,
      default: 0,
    },
  },
  components: {
    Loader,
    VueSlider,
  },
  data() {
    return {
      resultsPreview: 0,
    };
  },
};
</script>
<style lang="scss" scoped>
.chessboard_manager {
  display: flex;
  flex-direction: column;
  justify-content: space-between;

  min-width: 240px;
  margin-right: -16px;
  padding: 0px 32px 8px 16px;
  border: 1px solid #efefef;

  background-color: white;
  border-radius: 6px;

  .title {
    text-align: left;
  }

  input {
    color: gray;
    font-size: 1.4rem;

    padding: 8px;
    border: 1px solid #efefef;
    border-radius: 4px;
  }

  p {
    margin: 0px;
  }

  button {
    padding: 12px;
    border: 1px solid #efefef;

    background-color: white;
    outline: none;
  }

  section {
    display: flex;
    flex-direction: column;
  }
}

.chessboard_results {
  display: flex;

  dl.server_response {
    font: 1rem Inconsolata, monospace;
    color: white;
    text-shadow: 0 0 5px #c8c8c8;

    padding: 12px;
    margin: 0px;

    border-radius: 4px;
    background-color: black;

    dt,
    dd {
      margin: 0px;
    }

    dt::before {
      content: ">> ";
    }

    .success {
      color: #4af626;
    }
  }
}
</style>
