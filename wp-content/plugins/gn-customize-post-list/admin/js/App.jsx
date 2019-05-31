import React, { Component, Fragment } from 'react';
import ReactDOM from 'react-dom';
import '../css/style.css';
import deepcopy from 'deepcopy';
import axios from 'axios';
import qs from 'qs';
import jump from 'jump.js';

// component
import ErrorTitle from './component/ErrorTitle';
import Loading from './component/Loading';

// script
const OPTIONS = window.gncpl_admin_selects;
const DEFAULT_OPTION = window.gncpl_admin_default_option;
const DEFAULT_OPTIONS = window.gncpl_admin_default_options;
const SELECT_MAX_LENGTH = 6;

// dom
const root = document.createElement('div');
root.classList.add('gncpl-root');
document.body.appendChild(root);

// react class
class App extends Component {
  constructor() {
    super();
    this.state = {
      types: window.gncpl_admin_post_types,
      options: window.gncpl_admin_options,
      errors: {
        title: '',
        className: '',
        list: []
      },
      loading: false
    };
    this.addColumn = this.addColumn.bind(this);
    this.deleteColumn = this.deleteColumn.bind(this);
    this.updateText = this.updateText.bind(this);
    this.updateSelect = this.updateSelect.bind(this);
    this.resetSelect = this.resetSelect.bind(this);
    this.checkSelectType = this.checkSelectType.bind(this);
    this.updateOptions = this.updateOptions.bind(this);
  }
  componentDidMount() {
    this.init();
  }
  init() {
    let newOptions = {};
    this.state.types.forEach(item => {
      if (!this.state.options[item.name]) {
        newOptions[item.name] = deepcopy(DEFAULT_OPTIONS);
      }
    });
    this.setState(prevState => {
      return {
        options: Object.assign({}, prevState.options, newOptions)
      };
    });
  }
  addColumn(type) {
    if (this.state.options[type].length >= SELECT_MAX_LENGTH) return;
    this.setState(prevState => {
      prevState.options[type].push({ ...DEFAULT_OPTION });
      return {
        options: prevState.options
      };
    });
  }
  deleteColumn(type, i) {
    this.setState(prevState => {
      prevState.options[type].splice(i, 1);
      return {
        options: prevState.options
      };
    });
  }
  updateText(which, e, type, i) {
    const value = e.target.value;
    this.setState(prevState => {
      prevState.options[type][i][which] = value;
      return {
        options: prevState.options
      };
    });
  }
  updateSelect(e, type, i) {
    let val = e.target.value;
    this.setState(prevState => {
      prevState.options[type][i] = {
        key: val,
        label: '',
        value: ''
      };
      return {
        options: prevState.options
      };
    });
  }
  resetSelect(type) {
    this.setState(prevState => {
      prevState.options[type] = deepcopy(DEFAULT_OPTIONS);
      return {
        options: prevState.options
      };
    });
  }
  checkSelectType(slug) {
    return ['taxonomy', 'custom_field_img', 'custom_field_text'].some(e => {
      return e === slug;
    });
  }
  updateOptions() {
    const data = {
      action: 'update_gncpl_options',
      security: window.gncpl_admin_security,
      gncpl_options: this.state.options
    };
    const options = {
      method: 'POST',
      data: qs.stringify(data),
      url: window.gncpl_admin_ajax_url
    };
    const interval = 500;

    this.setState({
      loading: true
    });

    axios(options)
      .then(response => {
        const { data } = response;
        let errors = {};

        if (typeof data === 'object') {
          errors = {
            title: 'There is a defect in the input data',
            className: 'is-error',
            list: data
          };
        } else if (data === 'security') {
          errors = {
            title: 'There was an illegal operation.',
            className: 'is-error',
            list: []
          };
        } else {
          errors = {
            title: 'update success',
            className: 'is-success',
            list: []
          };
        }
        setTimeout(() => {
          this.setState({ errors, loading: false }, this.scrollTop);
        }, interval);
      })
      .catch(error => {
        console.log(error);
        this.setState(
          {
            errors: {
              title: 'update failed',
              className: 'is-error',
              list: []
            },
            loading: false
          },
          this.scrollTop
        );
      });
  }
  scrollTop() {
    jump('.gncpl-admin-title', {
      duration: 300,
      offset: -100
    });
  }
  render() {
    return (
      <Fragment>
        {this.state.loading && <Loading />}
        {this.state.errors.title && <ErrorTitle errors={this.state.errors} />}
        {this.state.types.map(type => (
          <section className="gncpl-admin-section" key={type.name}>
            <h4 className="gncpl-admin-postType">
              ■post type : <span>[{type.label}]</span>
            </h4>
            {this.state.errors.list[type.name] && (
              <ul className="gncpl-admin-error-list">
                {Object.keys(this.state.errors.list[type.name]).map(key => {
                  return (
                    <li className="gncpl-admin-error-listChild" key={key}>
                      {Number(key) + 1}th row :{' '}
                      {this.state.errors.list[type.name][key]}
                    </li>
                  );
                })}
              </ul>
            )}
            <ul className="gncpl-admin-list">
              {this.state.options[type.name] &&
                this.state.options[type.name].map((select, selectIndex) => {
                  return (
                    <li className="gncpl-admin-listChild" key={selectIndex}>
                      <b>type :</b>
                      <select
                        className="gncpl-admin-listSelect"
                        value={select['key']}
                        onChange={e => {
                          this.updateSelect(e, type.name, selectIndex);
                        }}
                      >
                        {OPTIONS.map(item => {
                          return (
                            <option key={item.key} value={item.key}>
                              {item.label}
                            </option>
                          );
                        })}
                      </select>

                      {(() => {
                        if (this.checkSelectType(select.key)) {
                          return (
                            <Fragment>
                              <div>
                                <b>label : </b>
                                <input
                                  className="gncpl-admin-input"
                                  type="text"
                                  value={select.label}
                                  placeholder="please input label"
                                  onChange={e => {
                                    this.updateText(
                                      'label',
                                      e,
                                      type.name,
                                      selectIndex
                                    );
                                  }}
                                />
                              </div>
                              <div>
                                <b>slug : </b>
                                <input
                                  className="gncpl-admin-input"
                                  type="text"
                                  value={select.value}
                                  placeholder="please input slug"
                                  onChange={e => {
                                    this.updateText(
                                      'value',
                                      e,
                                      type.name,
                                      selectIndex
                                    );
                                  }}
                                />
                              </div>
                            </Fragment>
                          );
                        }
                      })()}
                      <p className="gncpl-admin-litDeleteWrap">
                        <button
                          onClick={e => {
                            e.preventDefault();
                            this.deleteColumn(type.name, selectIndex);
                          }}
                          className="button btn-danger button-large gncpl-admin-listDelete"
                        >
                          delete
                        </button>
                      </p>
                    </li>
                  );
                })}
            </ul>
            <button
              className="gncpl-admin-listAdd gncpl-admin-btn button button-primary button-large"
              onClick={e => {
                e.preventDefault();
                this.addColumn(type.name);
              }}
            >
              add row
            </button>
            <button
              className="gncpl-admin-listReset gncpl-admin-btn button button button-primary button-large"
              onClick={e => {
                e.preventDefault();
                this.resetSelect(type.name);
              }}
            >
              reset
            </button>
          </section>
        ))}
        <input
          className="gncpl-admin-submit gncpl-admin-btn button button button-primary button-large"
          type="submit"
          value="update"
          onClick={this.updateOptions}
        />
      </Fragment>
    );
  }
}

ReactDOM.render(<App />, document.getElementById('gncpl-admin-app'));
